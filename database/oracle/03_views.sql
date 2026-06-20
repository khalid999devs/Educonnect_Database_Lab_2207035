SET DEFINE OFF;

CREATE OR REPLACE VIEW vw_student_dashboard AS
SELECT
    s.id AS student_id,
    u.id AS user_id,
    u.name AS student_name,
    u.email,
    un.name AS university_name,
    af.name AS academic_field_name,
    s.department,
    s.semester,
    s.skill_level,
    (SELECT COUNT(*) FROM saved_resources sr WHERE sr.student_id = s.id) AS total_saved_resources,
    (SELECT COUNT(*) FROM saved_templates st WHERE st.student_id = s.id) AS total_saved_templates,
    (SELECT COUNT(*) FROM research_topics rt WHERE rt.student_id = s.id) AS total_research_topics,
    (SELECT COUNT(*) FROM academic_documents ad WHERE ad.student_id = s.id) AS total_uploaded_documents
FROM students s
JOIN users u ON u.id = s.user_id
JOIN universities un ON un.id = s.university_id
JOIN academic_fields af ON af.id = s.academic_field_id;

CREATE OR REPLACE VIEW vw_approved_resources AS
SELECT
    r.id AS resource_id,
    r.title,
    rc.name AS category_name,
    af.name AS academic_field_name,
    at.name AS task_name,
    r.resource_url,
    r.difficulty_level,
    r.average_rating,
    r.save_count,
    r.created_at
FROM resources r
JOIN resource_categories rc ON rc.id = r.resource_category_id
LEFT JOIN academic_fields af ON af.id = r.academic_field_id
LEFT JOIN academic_tasks at ON at.id = r.task_id
WHERE r.status = 'APPROVED';

CREATE OR REPLACE VIEW vw_template_marketplace AS
SELECT
    t.id AS template_id,
    t.title,
    tc.name AS category_name,
    un.name AS university_name,
    af.name AS academic_field_name,
    t.template_url,
    t.price,
    t.is_paid,
    t.average_rating,
    t.download_count,
    t.created_at
FROM templates t
JOIN template_categories tc ON tc.id = t.template_category_id
LEFT JOIN universities un ON un.id = t.university_id
LEFT JOIN academic_fields af ON af.id = t.academic_field_id
WHERE t.status = 'APPROVED';

CREATE OR REPLACE VIEW vw_student_research_summary AS
SELECT
    s.id AS student_id,
    u.name AS student_name,
    rt.id AS research_topic_id,
    rt.title AS research_topic,
    af.name AS academic_field_name,
    rt.status,
    COUNT(rc.id) AS total_collection_items
FROM research_topics rt
JOIN students s ON s.id = rt.student_id
JOIN users u ON u.id = s.user_id
LEFT JOIN academic_fields af ON af.id = rt.academic_field_id
LEFT JOIN research_collections rc ON rc.research_topic_id = rt.id
GROUP BY
    s.id,
    u.name,
    rt.id,
    rt.title,
    af.name,
    rt.status;

CREATE OR REPLACE VIEW vw_top_rated_resources AS
SELECT
    r.id AS resource_id,
    r.title,
    rc.name AS category_name,
    af.name AS academic_field_name,
    r.average_rating,
    r.save_count,
    DENSE_RANK() OVER (
        ORDER BY r.average_rating DESC, r.save_count DESC
    ) AS resource_rank
FROM resources r
JOIN resource_categories rc ON rc.id = r.resource_category_id
LEFT JOIN academic_fields af ON af.id = r.academic_field_id
WHERE r.status = 'APPROVED';
