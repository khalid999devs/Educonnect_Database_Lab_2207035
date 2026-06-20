SET DEFINE OFF;

CREATE OR REPLACE TRIGGER trg_resource_save_count
AFTER INSERT ON saved_resources
FOR EACH ROW
BEGIN
    UPDATE resources
    SET save_count = save_count + 1,
        updated_at = SYSDATE
    WHERE id = :NEW.resource_id;
END trg_resource_save_count;
/

CREATE OR REPLACE TRIGGER trg_template_download_count
AFTER INSERT ON template_purchases
FOR EACH ROW
WHEN (NEW.payment_status = 'PAID')
BEGIN
    UPDATE templates
    SET download_count = download_count + 1,
        updated_at = SYSDATE
    WHERE id = :NEW.template_id;
END trg_template_download_count;
/

CREATE OR REPLACE TRIGGER trg_resource_audit
AFTER UPDATE OR DELETE ON resources
FOR EACH ROW
DECLARE
    v_action_type audit_logs.action_type%TYPE;
    v_new_value CLOB;
BEGIN
    IF UPDATING THEN
        v_action_type := 'UPDATE';
        v_new_value :=
            'status=' || :NEW.status
            || '; save_count=' || TO_CHAR(:NEW.save_count)
            || '; average_rating=' || TO_CHAR(:NEW.average_rating);
    ELSE
        v_action_type := 'DELETE';
        v_new_value := NULL;
    END IF;

    INSERT INTO audit_logs (
        table_name,
        record_id,
        action_type,
        old_value,
        new_value
    ) VALUES (
        'RESOURCES',
        :OLD.id,
        v_action_type,
        'status=' || :OLD.status
            || '; save_count=' || TO_CHAR(:OLD.save_count)
            || '; average_rating=' || TO_CHAR(:OLD.average_rating),
        v_new_value
    );
END trg_resource_audit;
/

CREATE OR REPLACE TRIGGER trg_template_audit
AFTER UPDATE OR DELETE ON templates
FOR EACH ROW
DECLARE
    v_action_type audit_logs.action_type%TYPE;
    v_new_value CLOB;
BEGIN
    IF UPDATING THEN
        v_action_type := 'UPDATE';
        v_new_value :=
            'status=' || :NEW.status
            || '; download_count=' || TO_CHAR(:NEW.download_count)
            || '; average_rating=' || TO_CHAR(:NEW.average_rating);
    ELSE
        v_action_type := 'DELETE';
        v_new_value := NULL;
    END IF;

    INSERT INTO audit_logs (
        table_name,
        record_id,
        action_type,
        old_value,
        new_value
    ) VALUES (
        'TEMPLATES',
        :OLD.id,
        v_action_type,
        'status=' || :OLD.status
            || '; download_count=' || TO_CHAR(:OLD.download_count)
            || '; average_rating=' || TO_CHAR(:OLD.average_rating),
        v_new_value
    );
END trg_template_audit;
/

CREATE OR REPLACE TRIGGER trg_review_rating_update
FOR INSERT OR UPDATE OR DELETE ON reviews
COMPOUND TRIGGER
    TYPE t_id_set IS TABLE OF PLS_INTEGER INDEX BY PLS_INTEGER;

    g_resource_ids t_id_set;
    g_template_ids t_id_set;

    PROCEDURE collect_reviewable (
        p_reviewable_type IN reviews.reviewable_type%TYPE,
        p_reviewable_id   IN reviews.reviewable_id%TYPE
    )
    IS
    BEGIN
        IF p_reviewable_id IS NULL THEN
            RETURN;
        END IF;

        IF p_reviewable_type = 'RESOURCE' THEN
            g_resource_ids(p_reviewable_id) := 1;
        ELSIF p_reviewable_type = 'TEMPLATE' THEN
            g_template_ids(p_reviewable_id) := 1;
        END IF;
    END collect_reviewable;

    AFTER EACH ROW
    IS
    BEGIN
        IF INSERTING OR UPDATING THEN
            collect_reviewable(:NEW.reviewable_type, :NEW.reviewable_id);
        END IF;

        IF DELETING OR UPDATING THEN
            collect_reviewable(:OLD.reviewable_type, :OLD.reviewable_id);
        END IF;
    END AFTER EACH ROW;

    AFTER STATEMENT
    IS
        v_resource_id PLS_INTEGER;
        v_template_id PLS_INTEGER;
    BEGIN
        v_resource_id := g_resource_ids.FIRST;

        WHILE v_resource_id IS NOT NULL LOOP
            UPDATE resources
            SET average_rating = fn_resource_avg_rating(v_resource_id),
                updated_at = SYSDATE
            WHERE id = v_resource_id;

            v_resource_id := g_resource_ids.NEXT(v_resource_id);
        END LOOP;

        v_template_id := g_template_ids.FIRST;

        WHILE v_template_id IS NOT NULL LOOP
            UPDATE templates
            SET average_rating = fn_template_avg_rating(v_template_id),
                updated_at = SYSDATE
            WHERE id = v_template_id;

            v_template_id := g_template_ids.NEXT(v_template_id);
        END LOOP;
    END AFTER STATEMENT;
END trg_review_rating_update;
/
