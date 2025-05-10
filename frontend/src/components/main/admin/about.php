<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'admin') {
    header('Location: /thesis_project');
    exit();
}
?>

<div class="about-container">
    <h2>Manage About Content</h2>

    <div class="admin-controls">
        <button class="add-section-btn" data-action="showAddSectionModal">
            <i class="fas fa-plus"></i> Add New Section
        </button>
    </div>

    <div class="about-content" id="aboutContent">
    </div>
</div>

<div id="sectionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Section</h3>
            <button class="close-modal" data-action="closeModal">&times;</button>
        </div>
        <form id="sectionForm" class="modal-form">
            <input type="hidden" id="sectionId" name="sectionId">
            <div class="form-group">
                <label for="sectionTitle">Section Title</label>
                <input type="text" id="sectionTitle" name="sectionTitle" required>
            </div>
            <div class="form-group">
                <label for="sectionContent">Content</label>
                <textarea id="sectionContent" name="sectionContent" rows="6" required></textarea>
            </div>
            <div class="form-group">
                <label for="contentType">Content Type</label>
                <select id="contentType" name="contentType" required>
                    <option value="text">Text Only - For paragraphs and regular text content</option>
                    <option value="list">List Items - For bullet points (enter each item on a new line)</option>
                </select>
                <small class="content-type-help">
                    <strong>Text:</strong> Use for regular paragraphs and descriptive content.<br>
                    <strong>List:</strong> Use for bullet points. Enter each item on a new line in the content field.
                </small>
            </div>
            <div class="form-actions">
                <button type="button" class="cancel-btn" data-action="closeModal">Cancel</button>
                <button type="submit" class="submit-btn">Save Section</button>
            </div>
        </form>
    </div>
</div>

<style>
    @media (max-width: 768px) {}
</style>