<!-- Materials Modal -->
<div class="modal fade" id="materialsModal" tabindex="-1" aria-labelledby="materialsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="materialsModalLabel">
                    <i class="bi bi-folder-fill me-2"></i>Course Materials - <span id="courseName"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Upload Form (for teachers/admins) -->
                <div id="uploadSection" style="display: none;">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-upload me-2"></i>Upload New Material</h6>
                        </div>
                        <div class="card-body">
                            <form id="uploadForm" enctype="multipart/form-data">
                                <input type="hidden" id="uploadCourseId" name="course_id">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="material_file" class="form-label">Select File</label>
                                        <input type="file" class="form-control" id="material_file" name="material_file" required>
                                        <div class="form-text">
                                            Allowed: PDFs, DOC/DOCX, PPT/PPTX, ZIP/RAR, Images (JPG/PNG), Videos (MP4/AVI/MOV/etc.), TXT files. Max: 50MB
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="uploadDescription" class="form-label">Description (Optional)</label>
                                        <textarea class="form-control" id="uploadDescription" name="description" rows="2" placeholder="Brief description"></textarea>
                                    </div>
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                                    <button type="submit" class="btn btn-success" id="uploadBtn">
                                        <i class="bi bi-upload"></i> Upload Material
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <div id="messageArea" class="mb-3" style="display: none;"></div>

                <!-- Materials List -->
                <div id="materialsList">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Materials Modal -->
<script>
$(document).ready(function() {
    let currentCourseId = null;

    // Show materials modal
    window.showMaterialsModal = function(courseId, courseName, showUpload = false) {
        currentCourseId = courseId;
        $('#courseName').text(courseName);
        $('#uploadCourseId').val(courseId);

        // Show/hide upload section based on permissions
        if (showUpload) {
            $('#uploadSection').show();
        } else {
            $('#uploadSection').hide();
        }

        // Load materials
        loadMaterials(courseId);

        // Show modal
        $('#materialsModal').modal('show');
    };

    // Load materials via AJAX
    function loadMaterials(courseId) {
        $('#materialsList').html(`
            <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);

        $.ajax({
            url: '<?= base_url('materials/course/') ?>' + courseId,
            type: 'GET',
            success: function(response) {
                $('#materialsList').html(response);
            },
            error: function() {
                $('#materialsList').html(`
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-exclamation-triangle fs-3 mb-2"></i>
                        <p class="mb-0">Failed to load materials. Please try again.</p>
                    </div>
                `);
            }
        });
    }

    // Upload form submission
    $('#uploadForm').submit(function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        var uploadBtn = $('#uploadBtn');
        var originalText = uploadBtn.html();

        // Disable button and show loading
        uploadBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Uploading...');

        // Clear previous messages
        $('#messageArea').hide();

        $.ajax({
            url: '<?= base_url('materials/ajax-upload') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showMessage('success', response.message);

                    // Reset form
                    $('#uploadForm')[0].reset();

                    // Add new material directly to the list immediately
                    if (response.material) {
                        addMaterialToList(response.material);
                    } else {
                        // Fallback: reload materials list
                        loadMaterials(currentCourseId);
                    }
                } else {
                    showMessage('error', response.message);
                }
            },
            error: function(xhr) {
                let message = 'Upload failed. Please try again.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        message = response.message;
                    }
                } catch (e) {
                    // Use default message
                }
                showMessage('error', message);
            },
            complete: function() {
                // Re-enable button
                uploadBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Add new material to the list without reloading
    function addMaterialToList(material) {
        // Get current materials container
        var materialsContainer = $('#materialsList');

        // If currently showing "no materials" message, replace it with materials container
        if (materialsContainer.html().includes('No materials available')) {
            materialsContainer.html('<div class="row"></div>');
        }

        // Get row container (create if doesn't exist)
        var row = materialsContainer.find('.row');
        if (row.length === 0) {
            row = $('<div class="row"></div>');
            materialsContainer.html(row);
        }

        // Create material card HTML
        var materialCard = createMaterialCard(material);
        row.append(materialCard);
    }

    // Create material card HTML
    function createMaterialCard(material) {
        var fileType = material.file_type || material.file_name.split('.').pop() || 'unknown';
        var iconClass = 'bi bi-file-earmark';
        var iconColor = 'text-primary';

        // Determine icon based on file type
        switch (fileType.toLowerCase()) {
            case 'pdf': iconClass = 'bi bi-filetype-pdf'; iconColor = 'text-danger'; break;
            case 'doc':
            case 'docx': iconClass = 'bi bi-filetype-docx'; iconColor = 'text-primary'; break;
            case 'ppt':
            case 'pptx': iconClass = 'bi bi-filetype-pptx'; iconColor = 'text-warning'; break;
            case 'zip':
            case 'rar': iconClass = 'bi bi-filetype-zip'; iconColor = 'text-info'; break;
            case 'jpg':
            case 'jpeg':
            case 'png': iconClass = 'bi bi-filetype-img'; iconColor = 'text-success'; break;
            case 'txt': iconClass = 'bi bi-filetype-txt'; iconColor = 'text-secondary'; break;
            case 'mp4':
            case 'avi':
            case 'mov':
            case 'wmv':
            case 'flv':
            case 'mkv':
            case 'mpg':
            case 'mpeg': iconClass = 'bi bi-film'; iconColor = 'text-primary'; break;
        }

        var fileSize = material.file_size || 0;
        var uploadDate = new Date(material.created_at).toLocaleDateString('en-US', {
            year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
        });

        var description = material.description ? '<p class="card-text text-muted small">' + material.description + '</p>' : '';

        var deleteBtn = '';
        <?php if (session('userRole') === 'admin' || session('userRole') === 'teacher'): ?>
        deleteBtn = '<button class="btn btn-sm btn-outline-danger delete-material-btn" data-material-id="' + material.id + '">' +
                   '<i class="bi bi-trash"></i> Delete</button>';
        <?php endif; ?>

        var cardHtml = '<div class="col-md-6 mb-4">' +
            '<div class="card h-100">' +
                '<div class="card-body">' +
                    '<div class="d-flex align-items-start">' +
                        '<div class="me-3">' +
                            '<i class="' + iconClass + ' fs-2 ' + iconColor + '"></i>' +
                        '</div>' +
                        '<div class="flex-grow-1">' +
                            '<h5 class="card-title mb-1">' + material.file_name + '</h5>' +
                            '<p class="card-text text-muted small mb-2">' +
                                fileType.toUpperCase() + ' • ' + formatBytes(fileSize) + ' • ' +
                                'Uploaded: ' + uploadDate +
                            '</p>' +
                            description +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="card-footer bg-transparent border-top-0">' +
                    '<div class="d-flex justify-content-between">' +
                        '<a href="<?= base_url('materials/download/') ?>' + material.id + '" class="btn btn-sm btn-outline-primary">' +
                            '<i class="bi bi-download"></i> Download' +
                        '</a>' +
                        deleteBtn +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';

        return $(cardHtml);
    }

    // Format bytes function for JavaScript
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    // Show message function
    function showMessage(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';

        $('#messageArea')
            .removeClass('alert-success alert-danger')
            .addClass(`alert ${alertClass}`)
            .html(`<i class="bi bi-${icon} me-2"></i>${message}`)
            .fadeIn();

        // Auto-hide success messages after 3 seconds
        if (type === 'success') {
            setTimeout(function() {
                $('#messageArea').fadeOut();
            }, 3000);
        }
    }

    // Delete material
    $(document).on('click', '.delete-material-btn', function(e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to delete this material?')) {
            return;
        }

        const materialId = $(this).data('material-id');
        const btn = $(this);
        const originalHtml = btn.html();

        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Deleting...');

        $.ajax({
            url: '<?= base_url('materials/delete/') ?>' + materialId,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success || response.message) {
                    showMessage('success', response.message || 'Material deleted successfully!');
                    loadMaterials(currentCourseId);
                } else {
                    showMessage('error', response.message || 'Failed to delete material.');
                }
            },
            error: function(xhr) {
                let message = 'Failed to delete material. Please try again.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        message = response.message;
                    }
                } catch (e) {
                    // Use default message
                }
                showMessage('error', message);
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Reset modal when closed
    $('#materialsModal').on('hidden.bs.modal', function() {
        $('#uploadForm')[0].reset();
        $('#messageArea').hide();
        currentCourseId = null;
    });
});
</script>
