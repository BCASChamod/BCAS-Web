CREATE TABLE IF NOT EXISTS ui_elements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source VARCHAR(50) NOT NULL,
    type VARCHAR(50) NOT NULL,
    label VARCHAR(255) NOT NULL,
    action ENUM('link', 'custom_javascript') NOT NULL,
    action_rest VARCHAR(255) NOT NULL,
    helptext TEXT,
    custom_styles JSON DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


-- Dummy Data
INSERT INTO ui_elements (source, type, label, action, action_rest, helptext, custom_styles)
VALUES
('landing_btnbar', 'button', 'Need Guidance?', 'link', '#guidance_form', 'We are here to support you! Click here and fill out the form to connect with a student counsellor for personalized guidance and assistance.', '{"backgroundColor": "#f0f0f0", "color": "#333"}')
;
