SET DEFINE OFF;

DECLARE
    v_existing_rows NUMBER;
BEGIN
    SELECT
        (SELECT COUNT(*) FROM users) +
        (SELECT COUNT(*) FROM universities) +
        (SELECT COUNT(*) FROM resources) +
        (SELECT COUNT(*) FROM templates)
    INTO v_existing_rows
    FROM dual;

    IF v_existing_rows > 0 THEN
        RAISE_APPLICATION_ERROR(-20001, 'Seed data already exists. Run this script only on an empty EduConnect schema.');
    END IF;
END;
/

INSERT INTO universities (name, country, city, website_url)
VALUES ('Khulna University of Engineering & Technology', 'Bangladesh', 'Khulna', 'https://www.kuet.ac.bd');

INSERT INTO universities (name, country, city, website_url)
VALUES ('Bangladesh University of Engineering and Technology', 'Bangladesh', 'Dhaka', 'https://www.buet.ac.bd');

INSERT INTO universities (name, country, city, website_url)
VALUES ('University of Dhaka', 'Bangladesh', 'Dhaka', 'https://www.du.ac.bd');

INSERT INTO academic_fields (name, description)
VALUES ('Computer Science and Engineering', 'Software engineering, algorithms, databases, artificial intelligence, and computing systems.');

INSERT INTO academic_fields (name, description)
VALUES ('Electrical and Electronic Engineering', 'Electrical systems, electronics, communication, control, and signal processing.');

INSERT INTO academic_fields (name, description)
VALUES ('Chemistry', 'Organic, inorganic, physical, analytical, and applied chemistry.');

INSERT INTO academic_fields (name, description)
VALUES ('Business Administration', 'Management, accounting, finance, marketing, and entrepreneurship.');

INSERT INTO academic_fields (name, description)
VALUES ('Civil Engineering', 'Structural, geotechnical, transportation, environmental, and water resources engineering.');

INSERT INTO users (name, email, password, role, status)
VALUES ('Ayesha Rahman', 'ayesha.rahman@educonnect.test', '$2y$12$bmFXwjR7pDzvQ9wT3n93qecq.6YYVD9nCnf0mbFBqmXXWOu7WJnna', 'STUDENT', 'ACTIVE');

INSERT INTO users (name, email, password, role, status)
VALUES ('Nafis Ahmed', 'nafis.ahmed@educonnect.test', '$2y$12$bmFXwjR7pDzvQ9wT3n93qecq.6YYVD9nCnf0mbFBqmXXWOu7WJnna', 'STUDENT', 'ACTIVE');

INSERT INTO users (name, email, password, role, status)
VALUES ('Tasnim Islam', 'tasnim.islam@educonnect.test', '$2y$12$bmFXwjR7pDzvQ9wT3n93qecq.6YYVD9nCnf0mbFBqmXXWOu7WJnna', 'STUDENT', 'ACTIVE');

INSERT INTO users (name, email, password, role, status)
VALUES ('Farhan Karim', 'farhan.karim@educonnect.test', '$2y$12$bmFXwjR7pDzvQ9wT3n93qecq.6YYVD9nCnf0mbFBqmXXWOu7WJnna', 'CREATOR', 'ACTIVE');

INSERT INTO users (name, email, password, role, status)
VALUES ('Dr. Samira Hossain', 'samira.hossain@educonnect.test', '$2y$12$bmFXwjR7pDzvQ9wT3n93qecq.6YYVD9nCnf0mbFBqmXXWOu7WJnna', 'MENTOR', 'ACTIVE');

INSERT INTO users (name, email, password, role, status)
VALUES ('EduConnect Admin', 'admin@educonnect.test', '$2y$12$bmFXwjR7pDzvQ9wT3n93qecq.6YYVD9nCnf0mbFBqmXXWOu7WJnna', 'ADMIN', 'ACTIVE');

INSERT INTO students (user_id, university_id, academic_field_id, department, semester, skill_level, bio)
VALUES (
    (SELECT id FROM users WHERE email = 'ayesha.rahman@educonnect.test'),
    (SELECT id FROM universities WHERE name = 'Khulna University of Engineering & Technology'),
    (SELECT id FROM academic_fields WHERE name = 'Computer Science and Engineering'),
    'Computer Science and Engineering', '6', 'INTERMEDIATE',
    'Interested in backend development, database systems, and educational technology.'
);

INSERT INTO students (user_id, university_id, academic_field_id, department, semester, skill_level, bio)
VALUES (
    (SELECT id FROM users WHERE email = 'nafis.ahmed@educonnect.test'),
    (SELECT id FROM universities WHERE name = 'Bangladesh University of Engineering and Technology'),
    (SELECT id FROM academic_fields WHERE name = 'Electrical and Electronic Engineering'),
    'Electrical and Electronic Engineering', '4', 'BEGINNER',
    'Building practical skills in circuits, electronics, and embedded systems.'
);

INSERT INTO students (user_id, university_id, academic_field_id, department, semester, skill_level, bio)
VALUES (
    (SELECT id FROM users WHERE email = 'tasnim.islam@educonnect.test'),
    (SELECT id FROM universities WHERE name = 'University of Dhaka'),
    (SELECT id FROM academic_fields WHERE name = 'Chemistry'),
    'Chemistry', '8', 'ADVANCED',
    'Preparing for postgraduate research in green chemistry and nanomaterials.'
);

INSERT INTO student_preferences (student_id, goal_type, preference_key, preference_value)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'), 'PROJECT', 'primary_task', 'Project Development');

INSERT INTO student_preferences (student_id, goal_type, preference_key, preference_value)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'), 'CODING', 'preferred_language', 'PHP');

INSERT INTO student_preferences (student_id, goal_type, preference_key, preference_value)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'), 'RESEARCH', 'research_interest', 'Educational Technology');

INSERT INTO student_preferences (student_id, goal_type, preference_key, preference_value)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'nafis.ahmed@educonnect.test'), 'LAB', 'primary_task', 'Lab Report Preparation');

INSERT INTO student_preferences (student_id, goal_type, preference_key, preference_value)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'nafis.ahmed@educonnect.test'), 'EXAM', 'study_mode', 'Practice Problems');

INSERT INTO student_preferences (student_id, goal_type, preference_key, preference_value)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'nafis.ahmed@educonnect.test'), 'PROJECT', 'research_interest', 'Internet of Things');

INSERT INTO student_preferences (student_id, goal_type, preference_key, preference_value)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'), 'RESEARCH', 'primary_task', 'Research Paper Search');

INSERT INTO student_preferences (student_id, goal_type, preference_key, preference_value)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'), 'LAB', 'research_interest', 'Green Chemistry');

INSERT INTO student_preferences (student_id, goal_type, preference_key, preference_value)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'), 'PROJECT', 'output_format', 'Research Poster');

INSERT INTO academic_documents (student_id, title, document_type, file_name, file_path, file_mime_type, status)
VALUES (
    (SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'),
    'CSE Curriculum 2024', 'CURRICULUM', 'cse_curriculum_2024.pdf', '/demo/documents/cse_curriculum_2024.pdf', 'application/pdf', 'EXTRACTED'
);

INSERT INTO academic_documents (student_id, title, document_type, file_name, file_path, file_mime_type, status)
VALUES (
    (SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'),
    'Spring Class Routine', 'ROUTINE', 'spring_class_routine.pdf', '/demo/documents/spring_class_routine.pdf', 'application/pdf', 'UPLOADED'
);

INSERT INTO academic_documents (student_id, title, document_type, file_name, file_path, file_mime_type, status)
VALUES (
    (SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'nafis.ahmed@educonnect.test'),
    'Signals and Systems Lab Assignment', 'LAB_FILE', 'signals_lab_assignment.pdf', '/demo/documents/signals_lab_assignment.pdf', 'application/pdf', 'EXTRACTED'
);

INSERT INTO academic_documents (student_id, title, document_type, file_name, file_path, file_mime_type, status)
VALUES (
    (SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'),
    'Green Chemistry Research Paper', 'RESEARCH_PAPER', 'green_chemistry_paper.pdf', '/demo/documents/green_chemistry_paper.pdf', 'application/pdf', 'EXTRACTED'
);

INSERT INTO extracted_document_data (document_id, student_id, data_type, data_key, data_value, confidence_score)
VALUES (
    (SELECT id FROM academic_documents WHERE title = 'CSE Curriculum 2024'),
    (SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'),
    'SUBJECT', 'course', 'Database Systems', 0.96
);

INSERT INTO extracted_document_data (document_id, student_id, data_type, data_key, data_value, confidence_score)
VALUES (
    (SELECT id FROM academic_documents WHERE title = 'CSE Curriculum 2024'),
    (SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'),
    'TOPIC', 'topic', 'Relational Database Design', 0.91
);

INSERT INTO extracted_document_data (document_id, student_id, data_type, data_key, data_value, confidence_score)
VALUES (
    (SELECT id FROM academic_documents WHERE title = 'Signals and Systems Lab Assignment'),
    (SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'nafis.ahmed@educonnect.test'),
    'DEADLINE', 'submission_date', '2026-07-15', 0.94
);

INSERT INTO extracted_document_data (document_id, student_id, data_type, data_key, data_value, confidence_score)
VALUES (
    (SELECT id FROM academic_documents WHERE title = 'Signals and Systems Lab Assignment'),
    (SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'nafis.ahmed@educonnect.test'),
    'TOPIC', 'experiment', 'Fourier Series Analysis', 0.89
);

INSERT INTO extracted_document_data (document_id, student_id, data_type, data_key, data_value, confidence_score)
VALUES (
    (SELECT id FROM academic_documents WHERE title = 'Green Chemistry Research Paper'),
    (SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'),
    'RESEARCH_AREA', 'area', 'Green Nanotechnology', 0.97
);

INSERT INTO extracted_document_data (document_id, student_id, data_type, data_key, data_value, confidence_score)
VALUES (
    (SELECT id FROM academic_documents WHERE title = 'Green Chemistry Research Paper'),
    (SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'),
    'KEYWORD', 'keyword', 'Silver Nanoparticles', 0.95
);

INSERT INTO academic_tasks (name, description) VALUES ('Assignment Writing', 'Plan, write, format, and review academic assignments.');
INSERT INTO academic_tasks (name, description) VALUES ('Lab Report Preparation', 'Prepare structured laboratory reports with observations and analysis.');
INSERT INTO academic_tasks (name, description) VALUES ('Research Paper Search', 'Discover and evaluate scholarly papers and publications.');
INSERT INTO academic_tasks (name, description) VALUES ('Coding Practice', 'Practice programming concepts, algorithms, and implementation skills.');
INSERT INTO academic_tasks (name, description) VALUES ('Presentation Making', 'Create academic slides, posters, and presentation materials.');
INSERT INTO academic_tasks (name, description) VALUES ('Exam Preparation', 'Review courses, solve problems, and prepare for examinations.');
INSERT INTO academic_tasks (name, description) VALUES ('Project Development', 'Plan and build academic software, hardware, or research projects.');
INSERT INTO academic_tasks (name, description) VALUES ('Thesis Planning', 'Organize thesis topics, literature, methodology, and milestones.');

INSERT INTO tool_categories (name, description) VALUES ('Writing and Editing', 'Tools for academic writing, grammar, formatting, and editing.');
INSERT INTO tool_categories (name, description) VALUES ('Research and Discovery', 'Platforms for discovering scholarly publications and research.');
INSERT INTO tool_categories (name, description) VALUES ('Coding and Development', 'Tools for programming, source control, and software development.');
INSERT INTO tool_categories (name, description) VALUES ('Citation Management', 'Tools for collecting references and creating citations.');
INSERT INTO tool_categories (name, description) VALUES ('Data Analysis', 'Tools for datasets, statistics, notebooks, and visualization.');
INSERT INTO tool_categories (name, description) VALUES ('Design and Presentation', 'Tools for creating slides, diagrams, posters, and visual materials.');
INSERT INTO tool_categories (name, description) VALUES ('Collaboration', 'Tools for communication, teamwork, and shared academic work.');
INSERT INTO tool_categories (name, description) VALUES ('Productivity', 'Tools for planning, note-taking, scheduling, and task management.');

INSERT INTO tools (tool_category_id, name, description, website_url, academic_field_id, task_id, is_free, status)
VALUES ((SELECT id FROM tool_categories WHERE name = 'Writing and Editing'), 'Grammarly', 'Grammar and clarity assistant for academic writing.', 'https://www.grammarly.com', NULL, (SELECT id FROM academic_tasks WHERE name = 'Assignment Writing'), 1, 'APPROVED');

INSERT INTO tools (tool_category_id, name, description, website_url, academic_field_id, task_id, is_free, status)
VALUES ((SELECT id FROM tool_categories WHERE name = 'Research and Discovery'), 'Google Scholar', 'Search engine for scholarly literature across disciplines.', 'https://scholar.google.com', NULL, (SELECT id FROM academic_tasks WHERE name = 'Research Paper Search'), 1, 'APPROVED');

INSERT INTO tools (tool_category_id, name, description, website_url, academic_field_id, task_id, is_free, status)
VALUES ((SELECT id FROM tool_categories WHERE name = 'Coding and Development'), 'GitHub', 'Source control and collaboration platform for software projects.', 'https://github.com', (SELECT id FROM academic_fields WHERE name = 'Computer Science and Engineering'), (SELECT id FROM academic_tasks WHERE name = 'Project Development'), 1, 'APPROVED');

INSERT INTO tools (tool_category_id, name, description, website_url, academic_field_id, task_id, is_free, status)
VALUES ((SELECT id FROM tool_categories WHERE name = 'Writing and Editing'), 'Overleaf', 'Collaborative LaTeX editor for reports, papers, and theses.', 'https://www.overleaf.com', NULL, (SELECT id FROM academic_tasks WHERE name = 'Thesis Planning'), 1, 'APPROVED');

INSERT INTO tools (tool_category_id, name, description, website_url, academic_field_id, task_id, is_free, status)
VALUES ((SELECT id FROM tool_categories WHERE name = 'Citation Management'), 'Zotero', 'Open-source reference and citation manager.', 'https://www.zotero.org', NULL, (SELECT id FROM academic_tasks WHERE name = 'Research Paper Search'), 1, 'APPROVED');

INSERT INTO tools (tool_category_id, name, description, website_url, academic_field_id, task_id, is_free, status)
VALUES ((SELECT id FROM tool_categories WHERE name = 'Citation Management'), 'Mendeley', 'Reference manager and academic research network.', 'https://www.mendeley.com', NULL, (SELECT id FROM academic_tasks WHERE name = 'Research Paper Search'), 1, 'APPROVED');

INSERT INTO tools (tool_category_id, name, description, website_url, academic_field_id, task_id, is_free, status)
VALUES ((SELECT id FROM tool_categories WHERE name = 'Data Analysis'), 'Kaggle', 'Datasets, notebooks, and practical data science exercises.', 'https://www.kaggle.com', (SELECT id FROM academic_fields WHERE name = 'Computer Science and Engineering'), (SELECT id FROM academic_tasks WHERE name = 'Coding Practice'), 1, 'APPROVED');

INSERT INTO tools (tool_category_id, name, description, website_url, academic_field_id, task_id, is_free, status)
VALUES ((SELECT id FROM tool_categories WHERE name = 'Design and Presentation'), 'Canva', 'Design platform for academic slides and research posters.', 'https://www.canva.com', NULL, (SELECT id FROM academic_tasks WHERE name = 'Presentation Making'), 1, 'APPROVED');

INSERT INTO tools (tool_category_id, name, description, website_url, academic_field_id, task_id, is_free, status)
VALUES ((SELECT id FROM tool_categories WHERE name = 'Productivity'), 'Trello', 'Visual task boards for assignments and project planning.', 'https://trello.com', NULL, (SELECT id FROM academic_tasks WHERE name = 'Project Development'), 1, 'APPROVED');

INSERT INTO tools (tool_category_id, name, description, website_url, academic_field_id, task_id, is_free, status)
VALUES ((SELECT id FROM tool_categories WHERE name = 'Collaboration'), 'Microsoft Teams', 'Communication and collaboration platform for student groups.', 'https://www.microsoft.com/microsoft-teams', NULL, (SELECT id FROM academic_tasks WHERE name = 'Project Development'), 1, 'PENDING');

INSERT INTO resource_categories (name, description) VALUES ('Programming Tutorials', 'Tutorials and documentation for programming and software development.');
INSERT INTO resource_categories (name, description) VALUES ('Research Papers', 'Scholarly publications, journals, and academic writing resources.');
INSERT INTO resource_categories (name, description) VALUES ('Lecture Notes', 'Course notes, open courseware, and subject explanations.');
INSERT INTO resource_categories (name, description) VALUES ('Lab Guides', 'Laboratory manuals, experiment guides, and report instructions.');
INSERT INTO resource_categories (name, description) VALUES ('Exam Preparation', 'Practice problems, revision notes, and examination resources.');
INSERT INTO resource_categories (name, description) VALUES ('Project Resources', 'Guides, references, and assets for academic projects.');
INSERT INTO resource_categories (name, description) VALUES ('Scholarship and Career', 'Scholarship, internship, and career preparation resources.');
INSERT INTO resource_categories (name, description) VALUES ('Datasets', 'Public datasets for research, analysis, and project work.');

INSERT INTO resources (resource_category_id, academic_field_id, task_id, title, description, resource_url, difficulty_level, status, created_by)
VALUES ((SELECT id FROM resource_categories WHERE name = 'Programming Tutorials'), (SELECT id FROM academic_fields WHERE name = 'Computer Science and Engineering'), (SELECT id FROM academic_tasks WHERE name = 'Coding Practice'), 'Laravel Documentation', 'Official Laravel documentation for building modern PHP applications.', 'https://laravel.com/docs', 'BEGINNER', 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO resources (resource_category_id, academic_field_id, task_id, title, description, resource_url, difficulty_level, status, created_by)
VALUES ((SELECT id FROM resource_categories WHERE name = 'Programming Tutorials'), (SELECT id FROM academic_fields WHERE name = 'Computer Science and Engineering'), (SELECT id FROM academic_tasks WHERE name = 'Coding Practice'), 'Oracle SQL Language Reference', 'Official Oracle SQL reference for statements, expressions, and database objects.', 'https://docs.oracle.com/en/database/oracle/oracle-database/19/sqlrf/', 'INTERMEDIATE', 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO resources (resource_category_id, academic_field_id, task_id, title, description, resource_url, difficulty_level, status, created_by)
VALUES ((SELECT id FROM resource_categories WHERE name = 'Lecture Notes'), (SELECT id FROM academic_fields WHERE name = 'Computer Science and Engineering'), (SELECT id FROM academic_tasks WHERE name = 'Exam Preparation'), 'MIT OpenCourseWare Algorithms', 'Open lectures and materials for algorithms and computational problem solving.', 'https://ocw.mit.edu/courses/6-006-introduction-to-algorithms-fall-2011/', 'INTERMEDIATE', 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO resources (resource_category_id, academic_field_id, task_id, title, description, resource_url, difficulty_level, status, created_by)
VALUES ((SELECT id FROM resource_categories WHERE name = 'Research Papers'), (SELECT id FROM academic_fields WHERE name = 'Electrical and Electronic Engineering'), (SELECT id FROM academic_tasks WHERE name = 'Research Paper Search'), 'IEEE Xplore Digital Library', 'Research publications in electrical engineering, electronics, and computing.', 'https://ieeexplore.ieee.org', 'ADVANCED', 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO resources (resource_category_id, academic_field_id, task_id, title, description, resource_url, difficulty_level, status, created_by)
VALUES ((SELECT id FROM resource_categories WHERE name = 'Lecture Notes'), (SELECT id FROM academic_fields WHERE name = 'Electrical and Electronic Engineering'), (SELECT id FROM academic_tasks WHERE name = 'Exam Preparation'), 'All About Circuits Textbook', 'Open textbook covering electrical circuits, semiconductors, and digital systems.', 'https://www.allaboutcircuits.com/textbook/', 'BEGINNER', 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO resources (resource_category_id, academic_field_id, task_id, title, description, resource_url, difficulty_level, status, created_by)
VALUES ((SELECT id FROM resource_categories WHERE name = 'Lab Guides'), (SELECT id FROM academic_fields WHERE name = 'Computer Science and Engineering'), (SELECT id FROM academic_tasks WHERE name = 'Lab Report Preparation'), 'KUET CSE Lab Report Guide', 'Demo guide for structuring programming and database laboratory reports.', 'https://www.kuet.ac.bd/demo/cse-lab-report-guide', 'BEGINNER', 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO resources (resource_category_id, academic_field_id, task_id, title, description, resource_url, difficulty_level, status, created_by)
VALUES ((SELECT id FROM resource_categories WHERE name = 'Lecture Notes'), (SELECT id FROM academic_fields WHERE name = 'Chemistry'), (SELECT id FROM academic_tasks WHERE name = 'Exam Preparation'), 'Chemistry LibreTexts', 'Open chemistry textbooks and learning modules across major chemistry topics.', 'https://chem.libretexts.org', 'INTERMEDIATE', 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO resources (resource_category_id, academic_field_id, task_id, title, description, resource_url, difficulty_level, status, created_by)
VALUES ((SELECT id FROM resource_categories WHERE name = 'Datasets'), (SELECT id FROM academic_fields WHERE name = 'Chemistry'), (SELECT id FROM academic_tasks WHERE name = 'Research Paper Search'), 'PubChem', 'Open chemistry database containing compounds, substances, and bioassays.', 'https://pubchem.ncbi.nlm.nih.gov', 'ADVANCED', 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO resources (resource_category_id, academic_field_id, task_id, title, description, resource_url, difficulty_level, status, created_by)
VALUES ((SELECT id FROM resource_categories WHERE name = 'Datasets'), (SELECT id FROM academic_fields WHERE name = 'Computer Science and Engineering'), (SELECT id FROM academic_tasks WHERE name = 'Project Development'), 'Kaggle Datasets', 'Community datasets for machine learning, data analysis, and student projects.', 'https://www.kaggle.com/datasets', 'INTERMEDIATE', 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO resources (resource_category_id, academic_field_id, task_id, title, description, resource_url, difficulty_level, status, created_by)
VALUES ((SELECT id FROM resource_categories WHERE name = 'Project Resources'), (SELECT id FROM academic_fields WHERE name = 'Computer Science and Engineering'), (SELECT id FROM academic_tasks WHERE name = 'Project Development'), 'Project-Based Learning Guide', 'Planning framework for defining, implementing, and presenting academic projects.', 'https://www.pblworks.org/what-is-pbl', 'BEGINNER', 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO resources (resource_category_id, academic_field_id, task_id, title, description, resource_url, difficulty_level, status, created_by)
VALUES ((SELECT id FROM resource_categories WHERE name = 'Research Papers'), NULL, (SELECT id FROM academic_tasks WHERE name = 'Assignment Writing'), 'Academic Writing Handbook', 'Practical guidance for academic structure, evidence, citations, and revision.', 'https://writingcenter.unc.edu/tips-and-tools/', 'BEGINNER', 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO resources (resource_category_id, academic_field_id, task_id, title, description, resource_url, difficulty_level, status, created_by)
VALUES ((SELECT id FROM resource_categories WHERE name = 'Exam Preparation'), (SELECT id FROM academic_fields WHERE name = 'Civil Engineering'), (SELECT id FROM academic_tasks WHERE name = 'Exam Preparation'), 'Civil Engineering Formula Sheet', 'Demo collection of core structural and geotechnical formulas.', 'https://example.org/educonnect/civil-formula-sheet', 'INTERMEDIATE', 'PENDING', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO resources (resource_category_id, academic_field_id, task_id, title, description, resource_url, difficulty_level, status, created_by)
VALUES ((SELECT id FROM resource_categories WHERE name = 'Project Resources'), (SELECT id FROM academic_fields WHERE name = 'Business Administration'), (SELECT id FROM academic_tasks WHERE name = 'Presentation Making'), 'Business Case Study Collection', 'Curated business case studies for classroom analysis and presentations.', 'https://example.org/educonnect/business-case-studies', 'INTERMEDIATE', 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO resources (resource_category_id, academic_field_id, task_id, title, description, resource_url, difficulty_level, status, created_by)
VALUES ((SELECT id FROM resource_categories WHERE name = 'Scholarship and Career'), NULL, NULL, 'Bangladesh Scholarship Portal', 'Demo directory of scholarships and academic opportunities for university students.', 'https://example.org/educonnect/scholarships', 'BEGINNER', 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO resources (resource_category_id, academic_field_id, task_id, title, description, resource_url, difficulty_level, status, created_by)
VALUES ((SELECT id FROM resource_categories WHERE name = 'Exam Preparation'), (SELECT id FROM academic_fields WHERE name = 'Electrical and Electronic Engineering'), (SELECT id FROM academic_tasks WHERE name = 'Exam Preparation'), 'Digital Logic Practice Problems', 'Practice exercises covering Boolean algebra, gates, and sequential circuits.', 'https://example.org/educonnect/digital-logic-practice', 'INTERMEDIATE', 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO template_categories (name, description) VALUES ('Assignment Templates', 'Formats for university assignments and coursework.');
INSERT INTO template_categories (name, description) VALUES ('Lab Report Templates', 'Structured templates for laboratory reports.');
INSERT INTO template_categories (name, description) VALUES ('Research Paper Templates', 'Academic paper and publication templates.');
INSERT INTO template_categories (name, description) VALUES ('Presentation Templates', 'Slide and poster templates for academic presentations.');
INSERT INTO template_categories (name, description) VALUES ('Project Documentation', 'Proposal, requirements, and project report templates.');
INSERT INTO template_categories (name, description) VALUES ('Thesis Templates', 'Templates for undergraduate and postgraduate theses.');

INSERT INTO templates (template_category_id, university_id, academic_field_id, title, description, template_url, price, is_paid, status, created_by)
VALUES ((SELECT id FROM template_categories WHERE name = 'Assignment Templates'), (SELECT id FROM universities WHERE name = 'Khulna University of Engineering & Technology'), (SELECT id FROM academic_fields WHERE name = 'Computer Science and Engineering'), 'KUET Assignment Cover Page', 'Demo KUET-style cover page for course assignments.', 'https://example.org/templates/kuet-assignment-cover', 0, 0, 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO templates (template_category_id, university_id, academic_field_id, title, description, template_url, price, is_paid, status, created_by)
VALUES ((SELECT id FROM template_categories WHERE name = 'Lab Report Templates'), (SELECT id FROM universities WHERE name = 'Bangladesh University of Engineering and Technology'), (SELECT id FROM academic_fields WHERE name = 'Electrical and Electronic Engineering'), 'BUET Lab Report Format', 'Demo format for engineering laboratory reports.', 'https://example.org/templates/buet-lab-report', 0, 0, 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO templates (template_category_id, university_id, academic_field_id, title, description, template_url, price, is_paid, status, created_by)
VALUES ((SELECT id FROM template_categories WHERE name = 'Lab Report Templates'), (SELECT id FROM universities WHERE name = 'University of Dhaka'), (SELECT id FROM academic_fields WHERE name = 'Chemistry'), 'University of Dhaka Chemistry Lab Report', 'Demo chemistry experiment and observation report format.', 'https://example.org/templates/du-chemistry-lab-report', 0, 0, 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO templates (template_category_id, university_id, academic_field_id, title, description, template_url, price, is_paid, status, created_by)
VALUES ((SELECT id FROM template_categories WHERE name = 'Research Paper Templates'), NULL, (SELECT id FROM academic_fields WHERE name = 'Computer Science and Engineering'), 'IEEE Research Paper Template', 'Two-column research paper format inspired by IEEE publication structure.', 'https://example.org/templates/ieee-research-paper', 0, 0, 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO templates (template_category_id, university_id, academic_field_id, title, description, template_url, price, is_paid, status, created_by)
VALUES ((SELECT id FROM template_categories WHERE name = 'Research Paper Templates'), NULL, (SELECT id FROM academic_fields WHERE name = 'Business Administration'), 'APA Research Paper Template', 'APA-style academic paper format with headings and references.', 'https://example.org/templates/apa-research-paper', 0, 0, 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO templates (template_category_id, university_id, academic_field_id, title, description, template_url, price, is_paid, status, created_by)
VALUES ((SELECT id FROM template_categories WHERE name = 'Project Documentation'), (SELECT id FROM universities WHERE name = 'Khulna University of Engineering & Technology'), (SELECT id FROM academic_fields WHERE name = 'Computer Science and Engineering'), 'Final Year Project Proposal', 'Structured proposal for problem statement, objectives, methodology, and timeline.', 'https://example.org/templates/final-year-project-proposal', 299, 1, 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO templates (template_category_id, university_id, academic_field_id, title, description, template_url, price, is_paid, status, created_by)
VALUES ((SELECT id FROM template_categories WHERE name = 'Project Documentation'), NULL, (SELECT id FROM academic_fields WHERE name = 'Computer Science and Engineering'), 'Software Requirements Specification', 'Software requirements template for academic development projects.', 'https://example.org/templates/software-requirements-specification', 199, 1, 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO templates (template_category_id, university_id, academic_field_id, title, description, template_url, price, is_paid, status, created_by)
VALUES ((SELECT id FROM template_categories WHERE name = 'Presentation Templates'), NULL, NULL, 'Academic Presentation Deck', 'Reusable slide deck for coursework and research presentations.', 'https://example.org/templates/academic-presentation-deck', 99, 1, 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO templates (template_category_id, university_id, academic_field_id, title, description, template_url, price, is_paid, status, created_by)
VALUES ((SELECT id FROM template_categories WHERE name = 'Thesis Templates'), (SELECT id FROM universities WHERE name = 'Bangladesh University of Engineering and Technology'), (SELECT id FROM academic_fields WHERE name = 'Electrical and Electronic Engineering'), 'Undergraduate Thesis Template', 'Structured thesis chapters, front matter, and reference sections.', 'https://example.org/templates/undergraduate-thesis', 399, 1, 'APPROVED', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO templates (template_category_id, university_id, academic_field_id, title, description, template_url, price, is_paid, status, created_by)
VALUES ((SELECT id FROM template_categories WHERE name = 'Presentation Templates'), (SELECT id FROM universities WHERE name = 'University of Dhaka'), (SELECT id FROM academic_fields WHERE name = 'Chemistry'), 'Research Poster Template', 'Poster layout for methods, findings, charts, and conclusions.', 'https://example.org/templates/research-poster', 149, 1, 'PENDING', (SELECT id FROM users WHERE email = 'farhan.karim@educonnect.test'));

INSERT INTO saved_resources (student_id, resource_id)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'), (SELECT id FROM resources WHERE title = 'Laravel Documentation'));

INSERT INTO saved_resources (student_id, resource_id)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'), (SELECT id FROM resources WHERE title = 'Oracle SQL Language Reference'));

INSERT INTO saved_resources (student_id, resource_id)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'), (SELECT id FROM resources WHERE title = 'Project-Based Learning Guide'));

INSERT INTO saved_resources (student_id, resource_id)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'nafis.ahmed@educonnect.test'), (SELECT id FROM resources WHERE title = 'IEEE Xplore Digital Library'));

INSERT INTO saved_resources (student_id, resource_id)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'nafis.ahmed@educonnect.test'), (SELECT id FROM resources WHERE title = 'Digital Logic Practice Problems'));

INSERT INTO saved_resources (student_id, resource_id)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'), (SELECT id FROM resources WHERE title = 'Chemistry LibreTexts'));

INSERT INTO saved_resources (student_id, resource_id)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'), (SELECT id FROM resources WHERE title = 'PubChem'));

INSERT INTO saved_resources (student_id, resource_id)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'), (SELECT id FROM resources WHERE title = 'Academic Writing Handbook'));

INSERT INTO saved_templates (student_id, template_id)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'), (SELECT id FROM templates WHERE title = 'KUET Assignment Cover Page'));

INSERT INTO saved_templates (student_id, template_id)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'), (SELECT id FROM templates WHERE title = 'Final Year Project Proposal'));

INSERT INTO saved_templates (student_id, template_id)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'), (SELECT id FROM templates WHERE title = 'Software Requirements Specification'));

INSERT INTO saved_templates (student_id, template_id)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'nafis.ahmed@educonnect.test'), (SELECT id FROM templates WHERE title = 'BUET Lab Report Format'));

INSERT INTO saved_templates (student_id, template_id)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'), (SELECT id FROM templates WHERE title = 'University of Dhaka Chemistry Lab Report'));

INSERT INTO saved_templates (student_id, template_id)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'), (SELECT id FROM templates WHERE title = 'APA Research Paper Template'));

INSERT INTO template_purchases (student_id, template_id, amount, payment_status)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'), (SELECT id FROM templates WHERE title = 'Final Year Project Proposal'), 299, 'PAID');

INSERT INTO template_purchases (student_id, template_id, amount, payment_status)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'), (SELECT id FROM templates WHERE title = 'Software Requirements Specification'), 199, 'PAID');

INSERT INTO template_purchases (student_id, template_id, amount, payment_status)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'nafis.ahmed@educonnect.test'), (SELECT id FROM templates WHERE title = 'Undergraduate Thesis Template'), 399, 'PENDING');

INSERT INTO research_topics (student_id, title, description, academic_field_id, status)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'), 'AI-Powered Academic Recommendation System', 'Personalized recommendations based on student profiles, tasks, and saved resources.', (SELECT id FROM academic_fields WHERE name = 'Computer Science and Engineering'), 'IN_PROGRESS');

INSERT INTO research_topics (student_id, title, description, academic_field_id, status)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'nafis.ahmed@educonnect.test'), 'IoT-Based Smart Energy Monitoring', 'Low-cost energy monitoring using sensors, embedded systems, and cloud dashboards.', (SELECT id FROM academic_fields WHERE name = 'Electrical and Electronic Engineering'), 'READING');

INSERT INTO research_topics (student_id, title, description, academic_field_id, status)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'), 'Green Synthesis of Silver Nanoparticles', 'Environmentally responsible synthesis and characterization of silver nanoparticles.', (SELECT id FROM academic_fields WHERE name = 'Chemistry'), 'IN_PROGRESS');

INSERT INTO research_collections (research_topic_id, title, collection_type, resource_url, summary, keywords, reading_status)
VALUES ((SELECT id FROM research_topics WHERE title = 'AI-Powered Academic Recommendation System'), 'Survey of Recommender Systems', 'PAPER', 'https://example.org/research/recommender-systems-survey', 'Survey of collaborative, content-based, and hybrid recommendation approaches.', 'recommender systems, personalization, education', 'READ');

INSERT INTO research_collections (research_topic_id, title, collection_type, resource_url, summary, keywords, reading_status)
VALUES ((SELECT id FROM research_topics WHERE title = 'AI-Powered Academic Recommendation System'), 'EduConnect Recommendation Notes', 'NOTE', NULL, 'Initial scoring ideas using academic field, task preferences, ratings, and popularity.', 'scoring, student profile, resources', 'IMPORTANT');

INSERT INTO research_collections (research_topic_id, title, collection_type, resource_url, summary, keywords, reading_status)
VALUES ((SELECT id FROM research_topics WHERE title = 'IoT-Based Smart Energy Monitoring'), 'IoT Energy Monitoring Review', 'PAPER', 'https://example.org/research/iot-energy-monitoring', 'Review of sensors, communication protocols, and energy dashboard architectures.', 'IoT, energy, sensors', 'READING');

INSERT INTO research_collections (research_topic_id, title, collection_type, resource_url, summary, keywords, reading_status)
VALUES ((SELECT id FROM research_topics WHERE title = 'IoT-Based Smart Energy Monitoring'), 'Sample Household Energy Dataset', 'DATASET', 'https://archive.ics.uci.edu/dataset/235/individual+household+electric+power+consumption', 'Public household electric power consumption measurements.', 'energy consumption, time series, dataset', 'TO_READ');

INSERT INTO research_collections (research_topic_id, title, collection_type, resource_url, summary, keywords, reading_status)
VALUES ((SELECT id FROM research_topics WHERE title = 'Green Synthesis of Silver Nanoparticles'), 'Green Nanoparticle Synthesis Review', 'PAPER', 'https://example.org/research/green-nanoparticle-synthesis', 'Review of biological reducing agents and nanoparticle characterization methods.', 'green synthesis, silver nanoparticles, characterization', 'READ');

INSERT INTO research_collections (research_topic_id, title, collection_type, resource_url, summary, keywords, reading_status)
VALUES ((SELECT id FROM research_topics WHERE title = 'Green Synthesis of Silver Nanoparticles'), 'Nanoparticle Experiment Notes', 'NOTE', NULL, 'Candidate plant extracts, concentration ranges, and UV-Vis observation plan.', 'plant extract, UV-Vis, experiment', 'IMPORTANT');

INSERT INTO reviews (student_id, reviewable_type, reviewable_id, rating, comment_text)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'), 'RESOURCE', (SELECT id FROM resources WHERE title = 'Laravel Documentation'), 5, 'Clear and complete reference for Laravel development.');

INSERT INTO reviews (student_id, reviewable_type, reviewable_id, rating, comment_text)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'), 'RESOURCE', (SELECT id FROM resources WHERE title = 'Oracle SQL Language Reference'), 4, 'Detailed and useful, although dense for beginners.');

INSERT INTO reviews (student_id, reviewable_type, reviewable_id, rating, comment_text)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'ayesha.rahman@educonnect.test'), 'TEMPLATE', (SELECT id FROM templates WHERE title = 'KUET Assignment Cover Page'), 5, 'Useful formatting for regular course assignments.');

INSERT INTO reviews (student_id, reviewable_type, reviewable_id, rating, comment_text)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'nafis.ahmed@educonnect.test'), 'RESOURCE', (SELECT id FROM resources WHERE title = 'IEEE Xplore Digital Library'), 5, 'Excellent source for engineering research papers.');

INSERT INTO reviews (student_id, reviewable_type, reviewable_id, rating, comment_text)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'nafis.ahmed@educonnect.test'), 'RESOURCE', (SELECT id FROM resources WHERE title = 'Digital Logic Practice Problems'), 4, 'Good practice set for digital logic revision.');

INSERT INTO reviews (student_id, reviewable_type, reviewable_id, rating, comment_text)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'nafis.ahmed@educonnect.test'), 'TOOL', (SELECT id FROM tools WHERE name = 'GitHub'), 5, 'Very useful for managing team project code.');

INSERT INTO reviews (student_id, reviewable_type, reviewable_id, rating, comment_text)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'), 'RESOURCE', (SELECT id FROM resources WHERE title = 'Chemistry LibreTexts'), 5, 'Accessible explanations and helpful chemistry references.');

INSERT INTO reviews (student_id, reviewable_type, reviewable_id, rating, comment_text)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'), 'RESOURCE', (SELECT id FROM resources WHERE title = 'PubChem'), 4, 'Important database for compound and substance information.');

INSERT INTO reviews (student_id, reviewable_type, reviewable_id, rating, comment_text)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'), 'TEMPLATE', (SELECT id FROM templates WHERE title = 'University of Dhaka Chemistry Lab Report'), 5, 'The sections fit chemistry experiment reporting well.');

INSERT INTO reviews (student_id, reviewable_type, reviewable_id, rating, comment_text)
VALUES ((SELECT s.id FROM students s JOIN users u ON u.id = s.user_id WHERE u.email = 'tasnim.islam@educonnect.test'), 'TOOL', (SELECT id FROM tools WHERE name = 'Zotero'), 4, 'Helpful for organizing papers and generating citations.');

INSERT INTO audit_logs (table_name, record_id, action_type, old_value, new_value, changed_by)
VALUES ('RESOURCES', (SELECT id FROM resources WHERE title = 'Laravel Documentation'), 'APPROVE', 'PENDING', 'APPROVED', (SELECT id FROM users WHERE email = 'admin@educonnect.test'));

INSERT INTO audit_logs (table_name, record_id, action_type, old_value, new_value, changed_by)
VALUES ('TEMPLATES', (SELECT id FROM templates WHERE title = 'Final Year Project Proposal'), 'APPROVE', 'PENDING', 'APPROVED', (SELECT id FROM users WHERE email = 'admin@educonnect.test'));

UPDATE resources r
SET r.save_count = (
        SELECT COUNT(*)
        FROM saved_resources sr
        WHERE sr.resource_id = r.id
    ),
    r.average_rating = NVL((
        SELECT ROUND(AVG(rv.rating), 2)
        FROM reviews rv
        WHERE rv.reviewable_type = 'RESOURCE'
          AND rv.reviewable_id = r.id
    ), 0);

UPDATE templates t
SET t.download_count = (
        SELECT COUNT(*)
        FROM template_purchases tp
        WHERE tp.template_id = t.id
          AND tp.payment_status = 'PAID'
    ),
    t.average_rating = NVL((
        SELECT ROUND(AVG(rv.rating), 2)
        FROM reviews rv
        WHERE rv.reviewable_type = 'TEMPLATE'
          AND rv.reviewable_id = t.id
    ), 0);

COMMIT;
