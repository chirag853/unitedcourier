<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta name="robots" content="index, follow">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Daterangepicker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
    <style>
        .table-actions {
            display: flex;
            gap: 5px;
        }
        .action-btn {
            padding: 5px 10px;
            font-size: 12px;
        }
        .text-truncate-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .content-preview {
            max-width: 300px;
            max-height: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .dynamic-input-group {
            display: flex;
            gap: 5px;
            margin-bottom: 5px;
        }
        .dynamic-input-group input {
            flex: 1;
        }
        .dynamic-input-group button {
            padding: 5px 10px;
        }
    </style>
</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        @include('admin.partials.header')

        <!-- Search Modal -->
        <div class="modal fade" id="searchModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content bg-transparent">
                    <div class="card shadow-none mb-0">
                        <div class="px-3 py-2 d-flex flex-row align-items-center" id="search-top">
                            <i class="ti ti-search fs-22"></i>
                            <input type="search" class="form-control border-0" placeholder="Search">
                            <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close"><i class="ti ti-x fs-22"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.partials.sidebar')

        <!-- ========================
            Start Page Content
        ========================= -->
         
        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content pb-0">

                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1">Change Contact Page</h4>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                    </div>
                </div>                
                <!-- End Page Header -->

                <!-- Contact Page Content Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Contact Page Content Management</h5>
                                <p class="card-text">View and Edit all Contact page content sections</p>
                            </div>
                            <div class="card-body">
                                @if ($message = Session::get('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="ti ti-circle-check me-2"></i>
                                        {{ $message }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                               
                                <div class="table-responsive">
                                    <table class="table table-hover" id="contactContentTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Section Key</th>
                                                <th>Title</th>
                                                <th>Content Preview</th>
                                                <th>Sort Order</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($contactContent as $content)
                                                <tr>
                                                    <td>{{ $content->id }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $content->section_key }}</span>
                                                    </td>
                                                    <td>
                                                        <strong>{!! $content->title ?: '-' !!}</strong>
                                                    </td>
                                                    <td>
                                                        <div class="content-preview">
                                                            @if($content->paragraphs)
                                                                <small>{{ \Illuminate\Support\Str::limit($content->paragraphs, 80) }}</small>
                                                            @elseif($content->address)
                                                                <small>{{ \Illuminate\Support\Str::limit($content->address, 80) }}</small>
                                                            @elseif($content->phone_numbers && count($content->phone_numbers) > 0)
                                                                <small>Phone: {{ implode(', ', $content->phone_numbers) }}</small>
                                                            @elseif($content->email_addresses && count($content->email_addresses) > 0)
                                                                <small>Email: {{ implode(', ', $content->email_addresses) }}</small>
                                                            @else
                                                                <small class="text-muted">No content</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>{{ $content->sort_order }}</td>
                                                    <td>
                                                        <div class="table-actions">
                                                            <button type="button"
                                                                    class="btn btn-sm btn-primary action-btn edit-content-btn"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editModal"
                                                                    data-id="{{ $content->id }}"
                                                                    data-section-key="{{ $content->section_key }}"
                                                                    data-title="{{ $content->title ?? '' }}"
                                                                    data-paragraphs="{{ $content->paragraphs ?? '' }}"
                                                                    data-address="{{ $content->address ?? '' }}"
                                                                    data-map-embed-url="{{ $content->map_embed_url ?? '' }}"
                                                                    data-sort-order="{{ $content->sort_order }}"
                                                                    data-phone-numbers="{{ json_encode($content->phone_numbers ?? []) }}"
                                                                    data-email-addresses="{{ json_encode($content->email_addresses ?? []) }}"
                                                                    data-list-items="{{ json_encode($content->list_items ?? []) }}"
                                                                    data-social-links="{{ json_encode($content->social_links ?? []) }}">
                                                                <i class="ti ti-edit"></i> Edit
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger action-btn" onclick="deleteContent({{ $content->id }})">
                                                                <i class="ti ti-trash"></i> Delete
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <p class="text-muted">No contact content found. The contact page is empty.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- End Content -->

        </div>
        <!-- End Page Wrapper -->

    </div>
    <!-- End Wrapper -->

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit Contact Page Content</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="contentId" name="id">
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sectionKey" class="form-label">Section Key</label>
                                    <select class="form-control" id="sectionKey" name="section_key" required>
                                        <option value="page_meta">Page Meta</option>
                                        <option value="contact_info">Contact Info</option>
                                        <option value="hero">Hero</option>
                                        <option value="form">Form</option>
                                        <option value="map">Map</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Section title">
                                </div>

                                <div class="mb-3">
                                    <label for="sortOrder" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="sortOrder" name="sort_order" placeholder="1" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="paragraphs" class="form-label">Paragraphs</label>
                                    <textarea class="form-control" id="paragraphs" name="paragraphs" rows="3" placeholder="Main content text"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2" placeholder="Office address"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="mapEmbedUrl" class="form-label">Map Embed URL</label>
                                    <input type="text" class="form-control" id="mapEmbedUrl" name="map_embed_url" placeholder="Google Maps embed URL">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone Numbers</label>
                                    <div id="phoneNumbersContainer"></div>
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addPhoneInput()">
                                        <i class="ti ti-plus"></i> Add Phone
                                    </button>
                                    <input type="hidden" id="phoneNumbers" name="phone_numbers">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email Addresses</label>
                                    <div id="emailAddressesContainer"></div>
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addEmailInput()">
                                        <i class="ti ti-plus"></i> Add Email
                                    </button>
                                    <input type="hidden" id="emailAddresses" name="email_addresses">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">List Items</label>
                                    <div id="listItemsContainer"></div>
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addListItemInput()">
                                        <i class="ti ti-plus"></i> Add Item
                                    </button>
                                    <input type="hidden" id="listItems" name="list_items">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Social Links</label>
                                    <div id="socialLinksContainer"></div>
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addSocialLinkInput()">
                                        <i class="ti ti-plus"></i> Add Social Link
                                    </button>
                                    <input type="hidden" id="socialLinks" name="social_links">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Datatable JS -->
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>

    <!-- Tabler Icons -->
    <script src="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.js') }}"></script>

    <!-- ChartJS -->
    <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <!--  -->
    <!-- Daterangepikcer JS -->
	<script src="{{ asset('js/moment.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>

    <!-- Apexchart JS -->
	<script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/plugins/apexchart/chart-data.js') }}" type="text/javascript"></script>

	<!-- Chart JS -->
	<script src="{{ asset('assets/plugins/peity/jquery.peity.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/plugins/peity/chart-data.js') }}" type="text/javascript"></script>
    
	<!-- Simplebar JS -->
	<script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Select2 JS -->
	<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}" type="text/javascript"></script>

    <!-- Main JS -->
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>


    <script>
    let dataTable = null;
    $(document).ready( function () {
        $('#contactContentTable').DataTable();
    } );

    // Dynamic input functions
    function addPhoneInput(value = '') {
        const container = document.getElementById('phoneNumbersContainer');
        const div = document.createElement('div');
        div.className = 'dynamic-input-group';
        div.innerHTML = `
            <input type="text" class="form-control" placeholder="Phone number" value="${value}">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">
                <i class="ti ti-trash"></i>
            </button>
        `;
        container.appendChild(div);
    }

    function addEmailInput(value = '') {
        const container = document.getElementById('emailAddressesContainer');
        const div = document.createElement('div');
        div.className = 'dynamic-input-group';
        div.innerHTML = `
            <input type="email" class="form-control" placeholder="Email address" value="${value}">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">
                <i class="ti ti-trash"></i>
            </button>
        `;
        container.appendChild(div);
    }

    function addListItemInput(value = '') {
        const container = document.getElementById('listItemsContainer');
        const div = document.createElement('div');
        div.className = 'dynamic-input-group';
        div.innerHTML = `
            <input type="text" class="form-control" placeholder="List item" value="${value}">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">
                <i class="ti ti-trash"></i>
            </button>
        `;
        container.appendChild(div);
    }

    function addSocialLinkInput(name = '', url = '') {
        const container = document.getElementById('socialLinksContainer');
        const div = document.createElement('div');
        div.className = 'dynamic-input-group';
        div.innerHTML = `
            <input type="text" class="form-control" placeholder="Platform name (e.g., Facebook)" value="${name}" style="flex: 1;">
            <input type="text" class="form-control" placeholder="URL" value="${url}" style="flex: 1;">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">
                <i class="ti ti-trash"></i>
            </button>
        `;
        container.appendChild(div);
    }

    function collectPhoneNumbers() {
        const inputs = document.querySelectorAll('#phoneNumbersContainer input');
        const values = Array.from(inputs).map(input => input.value).filter(v => v.trim());
        document.getElementById('phoneNumbers').value = JSON.stringify(values);
    }

    function collectEmailAddresses() {
        const inputs = document.querySelectorAll('#emailAddressesContainer input');
        const values = Array.from(inputs).map(input => input.value).filter(v => v.trim());
        document.getElementById('emailAddresses').value = JSON.stringify(values);
    }

    function collectListItems() {
        const inputs = document.querySelectorAll('#listItemsContainer input');
        const values = Array.from(inputs).map(input => input.value).filter(v => v.trim());
        document.getElementById('listItems').value = JSON.stringify(values);
    }

    function collectSocialLinks() {
        const groups = document.querySelectorAll('#socialLinksContainer .dynamic-input-group');
        const links = [];
        groups.forEach(group => {
            const inputs = group.querySelectorAll('input');
            if (inputs.length >= 2) {
                const name = inputs[0].value.trim();
                const url = inputs[1].value.trim();
                if (name && url) {
                    links.push({ name, url });
                }
            }
        });
        document.getElementById('socialLinks').value = JSON.stringify(links);
    }

    // Populate the edit modal from data attributes on the clicked button.
    // Using data attributes (HTML-entity-encoded by Blade) avoids breaking the
    // onclick attribute when fields contain quotes or HTML (e.g. hero title).
    function editContentFromButton(btn) {
        document.getElementById('contentId').value = btn.dataset.id;
        document.getElementById('sectionKey').value = btn.dataset.sectionKey;
        document.getElementById('title').value = btn.dataset.title;
        document.getElementById('paragraphs').value = btn.dataset.paragraphs;
        document.getElementById('address').value = btn.dataset.address;
        document.getElementById('mapEmbedUrl').value = btn.dataset.mapEmbedUrl;
        document.getElementById('sortOrder').value = btn.dataset.sortOrder;

        // Clear existing dynamic inputs
        document.getElementById('phoneNumbersContainer').innerHTML = '';
        document.getElementById('emailAddressesContainer').innerHTML = '';
        document.getElementById('listItemsContainer').innerHTML = '';
        document.getElementById('socialLinksContainer').innerHTML = '';

        // Parse and populate phone numbers
        const phoneNumbersJson = btn.dataset.phoneNumbers;
        try {
            const phoneNumbers = JSON.parse(phoneNumbersJson);
            if (Array.isArray(phoneNumbers)) {
                phoneNumbers.forEach(phone => addPhoneInput(phone));
            } else if (phoneNumbers) {
                addPhoneInput(phoneNumbers);
            }
        } catch (e) {
            if (phoneNumbersJson) addPhoneInput(phoneNumbersJson);
        }

        // Parse and populate email addresses
        const emailAddressesJson = btn.dataset.emailAddresses;
        try {
            const emailAddresses = JSON.parse(emailAddressesJson);
            if (Array.isArray(emailAddresses)) {
                emailAddresses.forEach(email => addEmailInput(email));
            } else if (emailAddresses) {
                addEmailInput(emailAddresses);
            }
        } catch (e) {
            if (emailAddressesJson) addEmailInput(emailAddressesJson);
        }

        // Parse and populate list items
        const listItemsJson = btn.dataset.listItems;
        try {
            const listItems = JSON.parse(listItemsJson);
            if (Array.isArray(listItems)) {
                listItems.forEach(item => addListItemInput(item));
            } else if (listItems) {
                addListItemInput(listItems);
            }
        } catch (e) {
            if (listItemsJson) addListItemInput(listItemsJson);
        }

        // Parse and populate social links
        const socialLinksJson = btn.dataset.socialLinks;
        try {
            const socialLinks = JSON.parse(socialLinksJson);
            if (Array.isArray(socialLinks)) {
                socialLinks.forEach(link => {
                    if (typeof link === 'object' && link !== null) {
                        addSocialLinkInput(link.name || '', link.url || '');
                    } else {
                        addSocialLinkInput(link, '');
                    }
                });
            } else if (socialLinks && typeof socialLinks === 'object') {
                Object.entries(socialLinks).forEach(([name, url]) => {
                    addSocialLinkInput(name, url);
                });
            }
        } catch (e) {
            if (socialLinksJson) addSocialLinkInput('', socialLinksJson);
        }
    }

    // Wire up all edit buttons via event delegation (handles re-renders too)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.edit-content-btn');
        if (btn) {
            editContentFromButton(btn);
        }
    });

    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Collect all dynamic inputs into JSON
        collectPhoneNumbers();
        collectEmailAddresses();
        collectListItems();
        collectSocialLinks();

        const id = document.getElementById('contentId').value;
        const formData = new FormData(this);
        
        fetch(`${BASE_URL}/admin/update-contact-page-content/${id}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success', function () { location.reload(); });
            } else {
                console.error('Server Error:', data);
                showAlert('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Network Error:', error);
            showAlert('Network error occurred. Please check your connection and try again.', 'error');
        });

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
        modal.hide();
    });

    function deleteContent(id) {
        if (confirm('Are you sure you want to delete this content?')) {
            fetch(`${BASE_URL}/admin/delete-contact-page-content/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success', function () { location.reload(); });
                } else {
                    showAlert('Error: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred while deleting content.', 'error');
            });
        }
    }
    </script>

</body>

</html>
