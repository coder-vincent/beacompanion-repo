<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'admin') {
    header('Location: /thesis_project');
    exit();
}
?>

<div class="faq-container">
    <h2>Manage FAQ Content</h2>

    <div class="admin-controls">
        <button type="button" class="add-faq-btn" data-action="showAddFaqModal">
            <i class="fas fa-plus"></i> Add New FAQ
        </button>
    </div>

    <div class="faq-content" id="faqContent">
    </div>
</div>

<div id="faqModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add New FAQ</h3>
            <button type="button" class="close-modal" data-action="closeModal">&times;</button>
        </div>
        <form id="faqForm" class="modal-form">
            <input type="hidden" id="faqId" name="faqId">
            <div class="form-group">
                <label for="faqCategory">Category</label>
                <select id="faqCategory" name="faqCategory" required>
                    <option value="general">General Questions</option>
                    <option value="administrative">Administrative Questions</option>
                    <option value="technical">Technical Questions</option>
                </select>
            </div>
            <div class="form-group">
                <label for="faqQuestion">Question</label>
                <input type="text" id="faqQuestion" name="faqQuestion" required>
            </div>
            <div class="form-group">
                <label for="faqAnswer">Answer</label>
                <textarea id="faqAnswer" name="faqAnswer" rows="6" required></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="cancel-btn" data-action="closeModal">Cancel</button>
                <button type="submit" class="submit-btn">Save FAQ</button>
            </div>
        </form>
    </div>
</div>