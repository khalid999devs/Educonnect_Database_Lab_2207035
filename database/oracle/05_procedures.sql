SET DEFINE OFF;

CREATE OR REPLACE PROCEDURE proc_save_resource (
    p_student_id  IN NUMBER,
    p_resource_id IN NUMBER
)
IS
    v_student_count NUMBER;
    v_resource_status resources.status%TYPE;
    v_duplicate_count NUMBER;
BEGIN
    SELECT COUNT(*)
    INTO v_student_count
    FROM students
    WHERE id = p_student_id;

    IF v_student_count = 0 THEN
        RAISE_APPLICATION_ERROR(-20001, 'Student does not exist.');
    END IF;

    BEGIN
        SELECT status
        INTO v_resource_status
        FROM resources
        WHERE id = p_resource_id;
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            RAISE_APPLICATION_ERROR(-20002, 'Resource does not exist.');
    END;

    IF v_resource_status <> 'APPROVED' THEN
        RAISE_APPLICATION_ERROR(-20003, 'Only approved resources can be saved.');
    END IF;

    SELECT COUNT(*)
    INTO v_duplicate_count
    FROM saved_resources
    WHERE student_id = p_student_id
      AND resource_id = p_resource_id;

    IF v_duplicate_count > 0 THEN
        RAISE_APPLICATION_ERROR(-20004, 'Resource is already saved by this student.');
    END IF;

    INSERT INTO saved_resources (student_id, resource_id)
    VALUES (p_student_id, p_resource_id);

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE;
END proc_save_resource;
/

CREATE OR REPLACE PROCEDURE proc_save_template (
    p_student_id  IN NUMBER,
    p_template_id IN NUMBER
)
IS
    v_student_count NUMBER;
    v_template_status templates.status%TYPE;
    v_duplicate_count NUMBER;
BEGIN
    SELECT COUNT(*)
    INTO v_student_count
    FROM students
    WHERE id = p_student_id;

    IF v_student_count = 0 THEN
        RAISE_APPLICATION_ERROR(-20011, 'Student does not exist.');
    END IF;

    BEGIN
        SELECT status
        INTO v_template_status
        FROM templates
        WHERE id = p_template_id;
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            RAISE_APPLICATION_ERROR(-20012, 'Template does not exist.');
    END;

    IF v_template_status <> 'APPROVED' THEN
        RAISE_APPLICATION_ERROR(-20013, 'Only approved templates can be saved.');
    END IF;

    SELECT COUNT(*)
    INTO v_duplicate_count
    FROM saved_templates
    WHERE student_id = p_student_id
      AND template_id = p_template_id;

    IF v_duplicate_count > 0 THEN
        RAISE_APPLICATION_ERROR(-20014, 'Template is already saved by this student.');
    END IF;

    INSERT INTO saved_templates (student_id, template_id)
    VALUES (p_student_id, p_template_id);

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE;
END proc_save_template;
/

CREATE OR REPLACE PROCEDURE proc_purchase_template (
    p_student_id  IN NUMBER,
    p_template_id IN NUMBER
)
IS
    v_student_count NUMBER;
    v_template_status templates.status%TYPE;
    v_is_paid templates.is_paid%TYPE;
    v_price templates.price%TYPE;
    v_purchase_count NUMBER;
    v_saved_count NUMBER;
BEGIN
    SELECT COUNT(*)
    INTO v_student_count
    FROM students
    WHERE id = p_student_id;

    IF v_student_count = 0 THEN
        RAISE_APPLICATION_ERROR(-20021, 'Student does not exist.');
    END IF;

    BEGIN
        SELECT status, is_paid, price
        INTO v_template_status, v_is_paid, v_price
        FROM templates
        WHERE id = p_template_id;
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            RAISE_APPLICATION_ERROR(-20022, 'Template does not exist.');
    END;

    IF v_template_status <> 'APPROVED' THEN
        RAISE_APPLICATION_ERROR(-20023, 'Only approved templates can be purchased.');
    END IF;

    IF v_is_paid <> 1 THEN
        RAISE_APPLICATION_ERROR(-20024, 'Only paid templates require a purchase.');
    END IF;

    SELECT COUNT(*)
    INTO v_purchase_count
    FROM template_purchases
    WHERE student_id = p_student_id
      AND template_id = p_template_id
      AND payment_status = 'PAID';

    IF v_purchase_count > 0 THEN
        RAISE_APPLICATION_ERROR(-20025, 'Template has already been purchased by this student.');
    END IF;

    INSERT INTO template_purchases (
        student_id,
        template_id,
        amount,
        payment_status
    ) VALUES (
        p_student_id,
        p_template_id,
        v_price,
        'PAID'
    );

    SELECT COUNT(*)
    INTO v_saved_count
    FROM saved_templates
    WHERE student_id = p_student_id
      AND template_id = p_template_id;

    IF v_saved_count = 0 THEN
        INSERT INTO saved_templates (student_id, template_id)
        VALUES (p_student_id, p_template_id);
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE;
END proc_purchase_template;
/

CREATE OR REPLACE PROCEDURE proc_add_extracted_data (
    p_document_id      IN NUMBER,
    p_student_id       IN NUMBER,
    p_data_type        IN VARCHAR2,
    p_data_key         IN VARCHAR2,
    p_data_value       IN CLOB,
    p_confidence_score IN NUMBER
)
IS
    v_document_student_id academic_documents.student_id%TYPE;
BEGIN
    BEGIN
        SELECT student_id
        INTO v_document_student_id
        FROM academic_documents
        WHERE id = p_document_id;
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            RAISE_APPLICATION_ERROR(-20031, 'Academic document does not exist.');
    END;

    IF v_document_student_id <> p_student_id THEN
        RAISE_APPLICATION_ERROR(-20032, 'Document does not belong to the supplied student.');
    END IF;

    IF p_confidence_score IS NULL
       OR p_confidence_score < 0
       OR p_confidence_score > 1 THEN
        RAISE_APPLICATION_ERROR(-20033, 'Confidence score must be between 0 and 1.');
    END IF;

    INSERT INTO extracted_document_data (
        document_id,
        student_id,
        data_type,
        data_key,
        data_value,
        confidence_score
    ) VALUES (
        p_document_id,
        p_student_id,
        UPPER(TRIM(p_data_type)),
        TRIM(p_data_key),
        p_data_value,
        p_confidence_score
    );

    UPDATE academic_documents
    SET status = 'EXTRACTED',
        updated_at = SYSDATE
    WHERE id = p_document_id;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE;
END proc_add_extracted_data;
/

CREATE OR REPLACE PROCEDURE proc_approve_resource (
    p_resource_id  IN NUMBER,
    p_admin_user_id IN NUMBER
)
IS
    v_admin_count NUMBER;
    v_old_status resources.status%TYPE;
BEGIN
    SELECT COUNT(*)
    INTO v_admin_count
    FROM users
    WHERE id = p_admin_user_id
      AND role = 'ADMIN'
      AND status = 'ACTIVE';

    IF v_admin_count = 0 THEN
        RAISE_APPLICATION_ERROR(-20041, 'An active administrator is required.');
    END IF;

    BEGIN
        SELECT status
        INTO v_old_status
        FROM resources
        WHERE id = p_resource_id
        FOR UPDATE;
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            RAISE_APPLICATION_ERROR(-20042, 'Resource does not exist.');
    END;

    IF v_old_status = 'APPROVED' THEN
        RAISE_APPLICATION_ERROR(-20043, 'Resource is already approved.');
    END IF;

    UPDATE resources
    SET status = 'APPROVED',
        updated_at = SYSDATE
    WHERE id = p_resource_id;

    INSERT INTO audit_logs (
        table_name,
        record_id,
        action_type,
        old_value,
        new_value,
        changed_by
    ) VALUES (
        'RESOURCES',
        p_resource_id,
        'APPROVE',
        v_old_status,
        'APPROVED',
        p_admin_user_id
    );

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE;
END proc_approve_resource;
/

CREATE OR REPLACE PROCEDURE proc_approve_template (
    p_template_id   IN NUMBER,
    p_admin_user_id IN NUMBER
)
IS
    v_admin_count NUMBER;
    v_old_status templates.status%TYPE;
BEGIN
    SELECT COUNT(*)
    INTO v_admin_count
    FROM users
    WHERE id = p_admin_user_id
      AND role = 'ADMIN'
      AND status = 'ACTIVE';

    IF v_admin_count = 0 THEN
        RAISE_APPLICATION_ERROR(-20051, 'An active administrator is required.');
    END IF;

    BEGIN
        SELECT status
        INTO v_old_status
        FROM templates
        WHERE id = p_template_id
        FOR UPDATE;
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            RAISE_APPLICATION_ERROR(-20052, 'Template does not exist.');
    END;

    IF v_old_status = 'APPROVED' THEN
        RAISE_APPLICATION_ERROR(-20053, 'Template is already approved.');
    END IF;

    UPDATE templates
    SET status = 'APPROVED',
        updated_at = SYSDATE
    WHERE id = p_template_id;

    INSERT INTO audit_logs (
        table_name,
        record_id,
        action_type,
        old_value,
        new_value,
        changed_by
    ) VALUES (
        'TEMPLATES',
        p_template_id,
        'APPROVE',
        v_old_status,
        'APPROVED',
        p_admin_user_id
    );

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE;
END proc_approve_template;
/
