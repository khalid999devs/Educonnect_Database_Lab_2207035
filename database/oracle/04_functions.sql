SET DEFINE OFF;

CREATE OR REPLACE FUNCTION fn_profile_completion (
    p_student_id IN NUMBER
) RETURN NUMBER
IS
    v_profile_exists       NUMBER := 0;
    v_university_selected  NUMBER := 0;
    v_field_selected       NUMBER := 0;
    v_semester_set         NUMBER := 0;
    v_preference_exists    NUMBER := 0;
    v_document_exists      NUMBER := 0;
    v_saved_resource_exists NUMBER := 0;
    v_research_topic_exists NUMBER := 0;
    v_completed_items      NUMBER := 0;
BEGIN
    SELECT
        COUNT(*),
        NVL(MAX(CASE WHEN s.university_id IS NOT NULL THEN 1 ELSE 0 END), 0),
        NVL(MAX(CASE WHEN s.academic_field_id IS NOT NULL THEN 1 ELSE 0 END), 0),
        NVL(MAX(CASE WHEN TRIM(s.semester) IS NOT NULL THEN 1 ELSE 0 END), 0)
    INTO
        v_profile_exists,
        v_university_selected,
        v_field_selected,
        v_semester_set
    FROM students s
    WHERE s.id = p_student_id;

    IF v_profile_exists = 0 THEN
        RETURN 0;
    END IF;

    SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
    INTO v_preference_exists
    FROM student_preferences
    WHERE student_id = p_student_id;

    SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
    INTO v_document_exists
    FROM academic_documents
    WHERE student_id = p_student_id;

    SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
    INTO v_saved_resource_exists
    FROM saved_resources
    WHERE student_id = p_student_id;

    SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
    INTO v_research_topic_exists
    FROM research_topics
    WHERE student_id = p_student_id;

    v_completed_items :=
        v_profile_exists
        + v_university_selected
        + v_field_selected
        + v_semester_set
        + v_preference_exists
        + v_document_exists
        + v_saved_resource_exists
        + v_research_topic_exists;

    RETURN LEAST(100, v_completed_items * 12.5);
END fn_profile_completion;
/

CREATE OR REPLACE FUNCTION fn_resource_avg_rating (
    p_resource_id IN NUMBER
) RETURN NUMBER
IS
    v_average_rating NUMBER;
BEGIN
    SELECT ROUND(NVL(AVG(r.rating), 0), 2)
    INTO v_average_rating
    FROM reviews r
    WHERE r.reviewable_type = 'RESOURCE'
      AND r.reviewable_id = p_resource_id;

    RETURN v_average_rating;
END fn_resource_avg_rating;
/

CREATE OR REPLACE FUNCTION fn_template_avg_rating (
    p_template_id IN NUMBER
) RETURN NUMBER
IS
    v_average_rating NUMBER;
BEGIN
    SELECT ROUND(NVL(AVG(r.rating), 0), 2)
    INTO v_average_rating
    FROM reviews r
    WHERE r.reviewable_type = 'TEMPLATE'
      AND r.reviewable_id = p_template_id;

    RETURN v_average_rating;
END fn_template_avg_rating;
/

CREATE OR REPLACE FUNCTION fn_count_saved_resources (
    p_student_id IN NUMBER
) RETURN NUMBER
IS
    v_saved_resource_count NUMBER;
BEGIN
    SELECT COUNT(*)
    INTO v_saved_resource_count
    FROM saved_resources
    WHERE student_id = p_student_id;

    RETURN v_saved_resource_count;
END fn_count_saved_resources;
/

CREATE OR REPLACE FUNCTION fn_recommendation_score (
    p_student_id  IN NUMBER,
    p_resource_id IN NUMBER
) RETURN NUMBER
IS
    c_field_weight      CONSTANT NUMBER := 40;
    c_task_weight       CONSTANT NUMBER := 30;
    c_rating_multiplier CONSTANT NUMBER := 4;
    c_save_count_limit  CONSTANT NUMBER := 10;

    v_student_field_id  students.academic_field_id%TYPE;
    v_resource_field_id resources.academic_field_id%TYPE;
    v_resource_task_id  resources.task_id%TYPE;
    v_resource_save_count resources.save_count%TYPE;
    v_task_match        NUMBER := 0;
    v_score             NUMBER := 0;
BEGIN
    SELECT academic_field_id
    INTO v_student_field_id
    FROM students
    WHERE id = p_student_id;

    SELECT academic_field_id, task_id, save_count
    INTO v_resource_field_id, v_resource_task_id, v_resource_save_count
    FROM resources
    WHERE id = p_resource_id;

    IF v_resource_field_id IS NOT NULL
       AND v_student_field_id = v_resource_field_id THEN
        v_score := v_score + c_field_weight;
    END IF;

    SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
    INTO v_task_match
    FROM student_preferences sp
    JOIN academic_tasks at
      ON UPPER(TRIM(sp.preference_value)) = UPPER(at.name)
    WHERE sp.student_id = p_student_id
      AND LOWER(sp.preference_key) = 'primary_task'
      AND at.id = v_resource_task_id;

    IF v_task_match = 1 THEN
        v_score := v_score + c_task_weight;
    END IF;

    v_score :=
        v_score
        + (fn_resource_avg_rating(p_resource_id) * c_rating_multiplier)
        + LEAST(NVL(v_resource_save_count, 0), c_save_count_limit);

    RETURN LEAST(100, GREATEST(0, ROUND(v_score, 2)));
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        RETURN 0;
END fn_recommendation_score;
/
