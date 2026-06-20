SET DEFINE OFF;

CREATE OR REPLACE PROCEDURE proc_field_resource_report
IS
    CURSOR c_field_resources IS
        SELECT
            af.name AS field_name,
            r.id AS resource_id,
            r.title AS resource_title,
            rc.name AS category_name,
            r.difficulty_level,
            r.average_rating
        FROM academic_fields af
        LEFT JOIN resources r
          ON r.academic_field_id = af.id
         AND r.status = 'APPROVED'
        LEFT JOIN resource_categories rc
          ON rc.id = r.resource_category_id
        ORDER BY af.name, r.average_rating DESC, r.title;

    v_current_field academic_fields.name%TYPE;
    v_field_count NUMBER := 0;
    v_resource_count NUMBER := 0;
BEGIN
    DBMS_OUTPUT.PUT_LINE('EDUCONNECT APPROVED RESOURCES BY ACADEMIC FIELD');
    DBMS_OUTPUT.PUT_LINE('==================================================');

    FOR resource_record IN c_field_resources LOOP
        IF v_current_field IS NULL
           OR v_current_field <> resource_record.field_name THEN
            v_current_field := resource_record.field_name;
            v_field_count := v_field_count + 1;
            DBMS_OUTPUT.PUT_LINE('');
            DBMS_OUTPUT.PUT_LINE('Field: ' || v_current_field);
        END IF;

        IF resource_record.resource_id IS NULL THEN
            DBMS_OUTPUT.PUT_LINE('  No approved resources.');
        ELSE
            v_resource_count := v_resource_count + 1;
            DBMS_OUTPUT.PUT_LINE(
                '  - ' || resource_record.resource_title
                || ' | Category: ' || resource_record.category_name
                || ' | Level: ' || resource_record.difficulty_level
                || ' | Rating: ' || TO_CHAR(resource_record.average_rating, 'FM990.00')
            );
        END IF;
    END LOOP;

    DBMS_OUTPUT.PUT_LINE('');
    DBMS_OUTPUT.PUT_LINE(
        'Summary: ' || v_field_count || ' fields, '
        || v_resource_count || ' approved field-specific resources.'
    );
END proc_field_resource_report;
/

CREATE OR REPLACE PROCEDURE proc_university_template_report
IS
    CURSOR c_university_templates IS
        SELECT
            un.name AS university_name,
            t.id AS template_id,
            t.title AS template_title,
            tc.name AS category_name,
            t.price,
            t.is_paid,
            t.average_rating
        FROM universities un
        LEFT JOIN templates t
          ON t.university_id = un.id
         AND t.status = 'APPROVED'
        LEFT JOIN template_categories tc
          ON tc.id = t.template_category_id
        ORDER BY un.name, t.is_paid, t.title;

    v_current_university universities.name%TYPE;
    v_university_count NUMBER := 0;
    v_template_count NUMBER := 0;
    v_price_label VARCHAR2(50);
BEGIN
    DBMS_OUTPUT.PUT_LINE('EDUCONNECT APPROVED TEMPLATES BY UNIVERSITY');
    DBMS_OUTPUT.PUT_LINE('==================================================');

    FOR template_record IN c_university_templates LOOP
        IF v_current_university IS NULL
           OR v_current_university <> template_record.university_name THEN
            v_current_university := template_record.university_name;
            v_university_count := v_university_count + 1;
            DBMS_OUTPUT.PUT_LINE('');
            DBMS_OUTPUT.PUT_LINE('University: ' || v_current_university);
        END IF;

        IF template_record.template_id IS NULL THEN
            DBMS_OUTPUT.PUT_LINE('  No approved university-specific templates.');
        ELSE
            v_template_count := v_template_count + 1;

            IF template_record.is_paid = 1 THEN
                v_price_label := 'BDT ' || TO_CHAR(template_record.price, 'FM9999990.00');
            ELSE
                v_price_label := 'FREE';
            END IF;

            DBMS_OUTPUT.PUT_LINE(
                '  - ' || template_record.template_title
                || ' | Category: ' || template_record.category_name
                || ' | Price: ' || v_price_label
                || ' | Rating: ' || TO_CHAR(template_record.average_rating, 'FM990.00')
            );
        END IF;
    END LOOP;

    DBMS_OUTPUT.PUT_LINE('');
    DBMS_OUTPUT.PUT_LINE(
        'Summary: ' || v_university_count || ' universities, '
        || v_template_count || ' approved university-specific templates.'
    );
END proc_university_template_report;
/

CREATE OR REPLACE PROCEDURE proc_top_rated_resources_report
IS
    CURSOR c_top_resources IS
        SELECT
            resource_rank,
            title,
            category_name,
            NVL(academic_field_name, 'General') AS academic_field_name,
            average_rating,
            save_count
        FROM vw_top_rated_resources
        WHERE resource_rank <= 5
        ORDER BY resource_rank, title;

    v_resource_count NUMBER := 0;
BEGIN
    DBMS_OUTPUT.PUT_LINE('EDUCONNECT TOP-RATED APPROVED RESOURCES');
    DBMS_OUTPUT.PUT_LINE('==================================================');

    FOR resource_record IN c_top_resources LOOP
        v_resource_count := v_resource_count + 1;
        DBMS_OUTPUT.PUT_LINE(
            'Rank ' || resource_record.resource_rank
            || ': ' || resource_record.title
            || ' | Field: ' || resource_record.academic_field_name
            || ' | Category: ' || resource_record.category_name
            || ' | Rating: ' || TO_CHAR(resource_record.average_rating, 'FM990.00')
            || ' | Saves: ' || resource_record.save_count
        );
    END LOOP;

    IF v_resource_count = 0 THEN
        DBMS_OUTPUT.PUT_LINE('No approved resources found.');
    ELSE
        DBMS_OUTPUT.PUT_LINE('');
        DBMS_OUTPUT.PUT_LINE('Summary: ' || v_resource_count || ' resources across the top five ranks.');
    END IF;
END proc_top_rated_resources_report;
/

CREATE OR REPLACE PROCEDURE proc_student_workspace_report (
    p_student_id IN NUMBER
)
IS
    CURSOR c_saved_resources IS
        SELECT
            r.title,
            rc.name AS category_name,
            r.average_rating
        FROM saved_resources sr
        JOIN resources r ON r.id = sr.resource_id
        JOIN resource_categories rc ON rc.id = r.resource_category_id
        WHERE sr.student_id = p_student_id
        ORDER BY sr.created_at DESC, r.title;

    CURSOR c_saved_templates IS
        SELECT
            t.title,
            tc.name AS category_name,
            t.price,
            t.is_paid
        FROM saved_templates st
        JOIN templates t ON t.id = st.template_id
        JOIN template_categories tc ON tc.id = t.template_category_id
        WHERE st.student_id = p_student_id
        ORDER BY st.created_at DESC, t.title;

    CURSOR c_research_topics IS
        SELECT
            rt.title,
            rt.status,
            COUNT(rc.id) AS collection_count
        FROM research_topics rt
        LEFT JOIN research_collections rc ON rc.research_topic_id = rt.id
        WHERE rt.student_id = p_student_id
        GROUP BY rt.id, rt.title, rt.status, rt.created_at
        ORDER BY rt.created_at DESC, rt.title;

    CURSOR c_documents IS
        SELECT title, document_type, status
        FROM academic_documents
        WHERE student_id = p_student_id
        ORDER BY created_at DESC, title;

    v_student_name vw_student_dashboard.student_name%TYPE;
    v_email vw_student_dashboard.email%TYPE;
    v_university_name vw_student_dashboard.university_name%TYPE;
    v_academic_field_name vw_student_dashboard.academic_field_name%TYPE;
    v_semester vw_student_dashboard.semester%TYPE;
    v_profile_completion NUMBER;
    v_item_count NUMBER := 0;
    v_price_label VARCHAR2(50);
BEGIN
    BEGIN
        SELECT
            student_name,
            email,
            university_name,
            academic_field_name,
            semester
        INTO
            v_student_name,
            v_email,
            v_university_name,
            v_academic_field_name,
            v_semester
        FROM vw_student_dashboard
        WHERE student_id = p_student_id;
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            RAISE_APPLICATION_ERROR(-20061, 'Student does not exist.');
    END;

    v_profile_completion := fn_profile_completion(p_student_id);

    DBMS_OUTPUT.PUT_LINE('EDUCONNECT STUDENT WORKSPACE');
    DBMS_OUTPUT.PUT_LINE('==================================================');
    DBMS_OUTPUT.PUT_LINE('Student: ' || v_student_name);
    DBMS_OUTPUT.PUT_LINE('Email: ' || v_email);
    DBMS_OUTPUT.PUT_LINE('University: ' || v_university_name);
    DBMS_OUTPUT.PUT_LINE('Academic field: ' || v_academic_field_name);
    DBMS_OUTPUT.PUT_LINE('Semester: ' || v_semester);
    DBMS_OUTPUT.PUT_LINE('Profile completion: ' || TO_CHAR(v_profile_completion, 'FM990.0') || '%');

    DBMS_OUTPUT.PUT_LINE('');
    DBMS_OUTPUT.PUT_LINE('SAVED RESOURCES');
    v_item_count := 0;

    FOR resource_record IN c_saved_resources LOOP
        v_item_count := v_item_count + 1;
        DBMS_OUTPUT.PUT_LINE(
            '  - ' || resource_record.title
            || ' | Category: ' || resource_record.category_name
            || ' | Rating: ' || TO_CHAR(resource_record.average_rating, 'FM990.00')
        );
    END LOOP;

    IF v_item_count = 0 THEN
        DBMS_OUTPUT.PUT_LINE('  None.');
    END IF;

    DBMS_OUTPUT.PUT_LINE('');
    DBMS_OUTPUT.PUT_LINE('SAVED TEMPLATES');
    v_item_count := 0;

    FOR template_record IN c_saved_templates LOOP
        v_item_count := v_item_count + 1;

        IF template_record.is_paid = 1 THEN
            v_price_label := 'BDT ' || TO_CHAR(template_record.price, 'FM9999990.00');
        ELSE
            v_price_label := 'FREE';
        END IF;

        DBMS_OUTPUT.PUT_LINE(
            '  - ' || template_record.title
            || ' | Category: ' || template_record.category_name
            || ' | Price: ' || v_price_label
        );
    END LOOP;

    IF v_item_count = 0 THEN
        DBMS_OUTPUT.PUT_LINE('  None.');
    END IF;

    DBMS_OUTPUT.PUT_LINE('');
    DBMS_OUTPUT.PUT_LINE('RESEARCH TOPICS');
    v_item_count := 0;

    FOR topic_record IN c_research_topics LOOP
        v_item_count := v_item_count + 1;
        DBMS_OUTPUT.PUT_LINE(
            '  - ' || topic_record.title
            || ' | Status: ' || topic_record.status
            || ' | Collection items: ' || topic_record.collection_count
        );
    END LOOP;

    IF v_item_count = 0 THEN
        DBMS_OUTPUT.PUT_LINE('  None.');
    END IF;

    DBMS_OUTPUT.PUT_LINE('');
    DBMS_OUTPUT.PUT_LINE('ACADEMIC DOCUMENTS');
    v_item_count := 0;

    FOR document_record IN c_documents LOOP
        v_item_count := v_item_count + 1;
        DBMS_OUTPUT.PUT_LINE(
            '  - ' || document_record.title
            || ' | Type: ' || document_record.document_type
            || ' | Status: ' || document_record.status
        );
    END LOOP;

    IF v_item_count = 0 THEN
        DBMS_OUTPUT.PUT_LINE('  None.');
    END IF;

    DBMS_OUTPUT.PUT_LINE('');
    DBMS_OUTPUT.PUT_LINE('Workspace report complete.');
END proc_student_workspace_report;
/
