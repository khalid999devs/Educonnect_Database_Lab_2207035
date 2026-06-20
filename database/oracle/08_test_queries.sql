SET DEFINE OFF
SET SERVEROUTPUT ON SIZE UNLIMITED
SET PAGESIZE 100
SET LINESIZE 240

PROMPT 1. BASIC SELECT

SELECT id, name, email, role, status
FROM users
ORDER BY id;

PROMPT 2. WHERE FILTERING

SELECT id, title, difficulty_level, average_rating
FROM resources
WHERE status = 'APPROVED'
  AND difficulty_level = 'INTERMEDIATE'
ORDER BY title;

PROMPT 3. LIKE SEARCH

SELECT id, title, resource_url
FROM resources
WHERE LOWER(title) LIKE '%research%'
ORDER BY title;

PROMPT 4. ORDER BY

SELECT id, title, average_rating, save_count
FROM resources
WHERE status = 'APPROVED'
ORDER BY average_rating DESC, save_count DESC, title;

PROMPT 5. AGGREGATE FUNCTIONS

SELECT
    COUNT(*) AS approved_resource_count,
    ROUND(AVG(average_rating), 2) AS average_resource_rating,
    MAX(save_count) AS highest_save_count
FROM resources
WHERE status = 'APPROVED';

PROMPT 6. GROUP BY

SELECT
    rc.name AS category_name,
    COUNT(r.id) AS resource_count,
    ROUND(AVG(r.average_rating), 2) AS category_average_rating
FROM resource_categories rc
LEFT JOIN resources r
  ON r.resource_category_id = rc.id
 AND r.status = 'APPROVED'
GROUP BY rc.id, rc.name
ORDER BY rc.name;

PROMPT 7. HAVING

SELECT
    af.name AS academic_field_name,
    COUNT(r.id) AS approved_resource_count
FROM academic_fields af
JOIN resources r ON r.academic_field_id = af.id
WHERE r.status = 'APPROVED'
GROUP BY af.id, af.name
HAVING COUNT(r.id) >= 2
ORDER BY approved_resource_count DESC, af.name;

PROMPT 8. INNER JOIN

SELECT
    r.title,
    rc.name AS category_name,
    af.name AS academic_field_name,
    at.name AS academic_task_name
FROM resources r
JOIN resource_categories rc ON rc.id = r.resource_category_id
JOIN academic_fields af ON af.id = r.academic_field_id
JOIN academic_tasks at ON at.id = r.task_id
WHERE r.status = 'APPROVED'
ORDER BY af.name, r.title;

PROMPT 9. LEFT OUTER JOIN

SELECT
    un.name AS university_name,
    t.title AS template_title,
    t.status AS template_status
FROM universities un
LEFT OUTER JOIN templates t ON t.university_id = un.id
ORDER BY un.name, t.title;

PROMPT 10. RIGHT OUTER JOIN

SELECT
    un.name AS university_name,
    u.name AS student_name,
    s.department,
    s.semester
FROM universities un
RIGHT OUTER JOIN students s ON s.university_id = un.id
JOIN users u ON u.id = s.user_id
ORDER BY student_name;

PROMPT 11. FULL OUTER JOIN

SELECT
    af.name AS academic_field_name,
    r.title AS resource_title,
    r.status AS resource_status
FROM academic_fields af
FULL OUTER JOIN resources r ON r.academic_field_id = af.id
ORDER BY academic_field_name, resource_title;

PROMPT 12. SUBQUERY

SELECT id, title, average_rating
FROM resources
WHERE average_rating > (
    SELECT AVG(average_rating)
    FROM resources
    WHERE status = 'APPROVED'
)
AND status = 'APPROVED'
ORDER BY average_rating DESC, title;

PROMPT 13. CORRELATED SUBQUERY

SELECT
    r.id,
    r.title,
    (
        SELECT COUNT(*)
        FROM saved_resources sr
        WHERE sr.resource_id = r.id
    ) AS calculated_save_count
FROM resources r
WHERE r.status = 'APPROVED'
ORDER BY calculated_save_count DESC, r.title;

PROMPT 14. VIEW QUERY

SELECT
    student_name,
    university_name,
    academic_field_name,
    total_saved_resources,
    total_saved_templates,
    total_research_topics,
    total_uploaded_documents
FROM vw_student_dashboard
ORDER BY student_name;

SELECT
    template_id,
    title,
    category_name,
    university_name,
    price,
    average_rating
FROM vw_template_marketplace
ORDER BY average_rating DESC, title;

PROMPT 15. UNION

SELECT title, 'RESOURCE' AS content_type
FROM resources
WHERE status = 'APPROVED'
UNION
SELECT title, 'TEMPLATE' AS content_type
FROM templates
WHERE status = 'APPROVED'
ORDER BY content_type, title;

PROMPT 16. FUNCTION EXECUTION

SELECT
    u.name AS student_name,
    fn_profile_completion(s.id) AS profile_completion,
    fn_count_saved_resources(s.id) AS saved_resource_count
FROM students s
JOIN users u ON u.id = s.user_id
ORDER BY u.name;

SELECT
    r.title,
    fn_resource_avg_rating(r.id) AS calculated_average_rating
FROM resources r
WHERE r.title IN (
    'Laravel Documentation',
    'IEEE Xplore Digital Library',
    'Academic Writing Handbook'
)
ORDER BY r.title;

SELECT
    r.title,
    fn_recommendation_score(
        (
            SELECT s.id
            FROM students s
            JOIN users u ON u.id = s.user_id
            WHERE u.email = 'ayesha.rahman@educonnect.test'
        ),
        r.id
    ) AS recommendation_score
FROM resources r
WHERE r.status = 'APPROVED'
ORDER BY recommendation_score DESC, r.title;

PROMPT 17. PROCEDURE EXECUTION WITH CLEANUP

DECLARE
    v_student_id students.id%TYPE;
    v_resource_id resources.id%TYPE;
    v_existing_count NUMBER;
    v_old_save_count resources.save_count%TYPE;
    v_old_updated_at resources.updated_at%TYPE;
    v_max_audit_id audit_logs.id%TYPE;
BEGIN
    SELECT s.id
    INTO v_student_id
    FROM students s
    JOIN users u ON u.id = s.user_id
    WHERE u.email = 'ayesha.rahman@educonnect.test';

    SELECT id, save_count, updated_at
    INTO v_resource_id, v_old_save_count, v_old_updated_at
    FROM resources
    WHERE title = 'IEEE Xplore Digital Library';

    SELECT COUNT(*)
    INTO v_existing_count
    FROM saved_resources
    WHERE student_id = v_student_id
      AND resource_id = v_resource_id;

    IF v_existing_count = 0 THEN
        SELECT NVL(MAX(id), 0)
        INTO v_max_audit_id
        FROM audit_logs;

        proc_save_resource(v_student_id, v_resource_id);

        DBMS_OUTPUT.PUT_LINE(
            'PASS proc_save_resource: save count is '
            || fn_count_saved_resources(v_student_id) || ' for the test student.'
        );

        DELETE FROM saved_resources
        WHERE student_id = v_student_id
          AND resource_id = v_resource_id;

        UPDATE resources
        SET save_count = v_old_save_count,
            updated_at = v_old_updated_at
        WHERE id = v_resource_id;

        DELETE FROM audit_logs
        WHERE id > v_max_audit_id
          AND table_name = 'RESOURCES'
          AND record_id = v_resource_id
          AND action_type = 'UPDATE';

        COMMIT;
        DBMS_OUTPUT.PUT_LINE('Procedure test cleanup complete.');
    ELSE
        DBMS_OUTPUT.PUT_LINE('Procedure test skipped because the resource is already saved.');
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE;
END;
/

PROMPT 18. RESOURCE SAVE-COUNT TRIGGER TEST

SAVEPOINT before_resource_trigger_test;

INSERT INTO saved_resources (student_id, resource_id)
SELECT
    s.id,
    r.id
FROM students s
JOIN users u ON u.id = s.user_id
CROSS JOIN resources r
WHERE u.email = 'ayesha.rahman@educonnect.test'
  AND r.title = 'IEEE Xplore Digital Library'
  AND NOT EXISTS (
      SELECT 1
      FROM saved_resources sr
      WHERE sr.student_id = s.id
        AND sr.resource_id = r.id
  );

SELECT
    r.title,
    r.save_count AS trigger_updated_save_count,
    (
        SELECT COUNT(*)
        FROM saved_resources sr
        WHERE sr.resource_id = r.id
    ) AS actual_save_count
FROM resources r
WHERE r.title = 'IEEE Xplore Digital Library';

ROLLBACK TO before_resource_trigger_test;

SELECT title, save_count AS restored_save_count
FROM resources
WHERE title = 'IEEE Xplore Digital Library';

PROMPT 19. REVIEW RATING TRIGGER TEST

SAVEPOINT before_review_trigger_test;

INSERT INTO reviews (
    student_id,
    reviewable_type,
    reviewable_id,
    rating,
    comment_text
)
SELECT
    s.id,
    'RESOURCE',
    r.id,
    3,
    'Phase 10 rollback-only trigger test.'
FROM students s
JOIN users u ON u.id = s.user_id
CROSS JOIN resources r
WHERE u.email = 'ayesha.rahman@educonnect.test'
  AND r.title = 'Academic Writing Handbook';

SELECT
    r.title,
    r.average_rating AS trigger_updated_rating,
    fn_resource_avg_rating(r.id) AS calculated_rating
FROM resources r
WHERE r.title = 'Academic Writing Handbook';

ROLLBACK TO before_review_trigger_test;

SELECT title, average_rating AS restored_rating
FROM resources
WHERE title = 'Academic Writing Handbook';

COMMIT;

PROMPT 20. CURSOR REPORT EXECUTION

BEGIN
    proc_field_resource_report;
END;
/

BEGIN
    proc_university_template_report;
END;
/

BEGIN
    proc_top_rated_resources_report;
END;
/

DECLARE
    v_student_id students.id%TYPE;
BEGIN
    SELECT s.id
    INTO v_student_id
    FROM students s
    JOIN users u ON u.id = s.user_id
    WHERE u.email = 'ayesha.rahman@educonnect.test';

    proc_student_workspace_report(v_student_id);
END;
/

PROMPT 21. FINAL DATA-INTEGRITY CHECK

SELECT
    (
        SELECT COUNT(*)
        FROM resources r
        WHERE r.save_count <> (
            SELECT COUNT(*)
            FROM saved_resources sr
            WHERE sr.resource_id = r.id
        )
    ) AS resource_save_mismatches,
    (
        SELECT COUNT(*)
        FROM templates t
        WHERE t.download_count <> (
            SELECT COUNT(*)
            FROM template_purchases tp
            WHERE tp.template_id = t.id
              AND tp.payment_status = 'PAID'
        )
    ) AS template_download_mismatches,
    (
        SELECT COUNT(*)
        FROM resources r
        WHERE r.average_rating <> fn_resource_avg_rating(r.id)
    ) AS resource_rating_mismatches,
    (
        SELECT COUNT(*)
        FROM templates t
        WHERE t.average_rating <> fn_template_avg_rating(t.id)
    ) AS template_rating_mismatches
FROM dual;
