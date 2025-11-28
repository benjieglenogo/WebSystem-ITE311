<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0 text-primary fw-bold">Courses</h1>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <form id="searchForm" class="d-flex">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control" placeholder="Search courses..." name="search_term">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<div id="coursesContainer" class="row">
    <?php if (!empty($courses)): ?>
        <?php foreach ($courses as $course): ?>
            <div class="col-md-4 mb-4">
                <div class="card course-card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $course['course_name'] ?></h5>
                        <p class="card-text"><?= $course['description'] ?></p>
                        <a href="/courses/view/<?= $course['id'] ?>" class="btn btn-primary">View Course</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center">
            <div class="alert alert-info">No courses available.</div>
        </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Remove client-side filtering to avoid conflict with server-side search

    // Server-side search with AJAX
    $('#searchForm').submit(function(e) {
        e.preventDefault();
        var searchTerm = $('#searchInput').val();
        $.ajax({
            url: '/courses/search',
            type: 'GET',
            data: { search_term: searchTerm },
            success: function(data) {
                var $coursesContainer = $('#coursesContainer');
                $coursesContainer.empty();
                if (data.length > 0) {
                    data.forEach(function(course) {
                        $coursesContainer.append(
                            '<div class="col-md-4 mb-4">' +
                                '<div class="card course-card">' +
                                    '<div class="card-body">' +
                                        '<h5 class="card-title">' + course.course_name + '</h5>' +
                                        '<p class="card-text">' + (course.description || 'No description available.') + '</p>' +
                                        '<a href="/courses/view/' + course.id + '" class="btn btn-primary">View Course</a>' +
                                    '</div>' +
                                '</div>' +
                            '</div>'
                        );
                    });
                } else {
                    $coursesContainer.html('<div class="col-12 text-center"><div class="alert alert-info">No courses found matching your search.</div></div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                $('#coursesContainer').html('<div class="col-12 text-center"><div class="alert alert-danger">An error occurred while searching. Please try again.</div></div>');
            }
        });
    });

    // Clear search and reload all courses when input is cleared
    $('#searchInput').on('input', function() {
        if ($(this).val() === '') {
            // Reload all courses
            $.ajax({
                url: '/courses',
                type: 'GET',
                success: function(data) {
                    var $coursesContainer = $('#coursesContainer');
                    $coursesContainer.empty();
                    if (data.length > 0) {
                        data.forEach(function(course) {
                            $coursesContainer.append(
                                '<div class="col-md-4 mb-4">' +
                                    '<div class="card course-card">' +
                                        '<div class="card-body">' +
                                            '<h5 class="card-title">' + course.course_name + '</h5>' +
                                            '<p class="card-text">' + (course.description || 'No description available.') + '</p>' +
                                            '<a href="/courses/view/' + course.id + '" class="btn btn-primary">View Course</a>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>'
                            );
                        });
                    } else {
                        $coursesContainer.html('<div class="col-12 text-center"><div class="alert alert-info">No courses available.</div></div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        }
    });
});
</script>

<?= $this->endSection() ?>
