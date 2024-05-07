<? include ("partial/header.php");
; ?>
<div class="card">
    <div class="card-datatable table-responsive p-4">
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
            <div class="row me-2">
                <div class="col-md-12">
                    <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                        <div class="dt-buttons m-2">
                            <button class="dt-button add-new btn btn-primary" tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">
                                <span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                                    <span class="d-none d-sm-inline-block">
                                        <?php echo ucfirst($i18n->_t("add_new_user")); ?>
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-100" id="dt-users" aria-describedby="DataTables_Table_0_info">
                <thead class="border-top">
                    <tr>
                        <th class="control sorting_disabled dtr-hidden" style="display: none;" aria-label="">ID</th>
                        <th class="sorting sorting_desc" aria-sort="descending"><? echo $i18n->_t('email'); ?></th>
                        <th class="sorting"><? echo $i18n->_t('phone'); ?></th>
                        <th class="sorting"><? echo $i18n->_t('fullname'); ?></th>
                        <th class="sorting"><? echo $i18n->_t('role'); ?></th>
                        <th class="sorting"><? echo $i18n->_t('status'); ?></th>
                        <th class="sorting_disabled"><? echo $i18n->_t('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="odd">
                        <td valign="top" colspan="6" class="dataTables_empty"><?php echo $i18n->_t("no_data_available_in_table"); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Offcanvas to add new user -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasAddUserLabel" class="offcanvas-title"><?php echo $i18n->_t("add_user"); ?></h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="<?php echo $i18n->_t("close"); ?>"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="add-new-user pt-0 fv-plugins-bootstrap5 fv-plugins-framework" id="addNewUserForm" onsubmit="return false" novalidate="novalidate">
                <div class="mb-3 fv-plugins-icon-container">
                    <label class="form-label" for="fullname">
                        <?php echo ucfirst($i18n->_t("fullname")); ?>
                    </label>
                    <input type="text" class="form-control" id="fullname" placeholder="John Doe" name="fullname" aria-label="John Doe">
                </div>
                <div class="mb-3 fv-plugins-icon-container">
                    <label class="form-label" for="add-user-email">
                        <?php echo ucfirst($i18n->_t("email")); ?>
                    </label>
                    <input type="text" id="email" class="form-control" placeholder="john.doe@example.com" aria-label="john.doe@example.com" name="email">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="phone">
                        <?php echo ucfirst($i18n->_t("phone")); ?>
                    </label>
                    <input type="text" id="phone" class="form-control phone-mask" placeholder="+1 (609) 988-44-11" aria-label="john.doe@example.com" name="phone">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">
                        <?php echo ucfirst($i18n->_t("password")); ?>
                    </label>
                    <input type="text" id="password" class="form-control" placeholder="Web Developer" aria-label="jdoe1" name="password">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="role">
                        <?php echo ucfirst($i18n->_t("role")); ?>
                    </label>
                    <select id="role" class="form-select">
                        <option value="subscriber">Subscriber</option>
                        <option value="editor">Editor</option>
                        <option value="maintainer">Maintainer</option>
                        <option value="author">Author</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit waves-effect waves-light">
                    <?php echo ucfirst($i18n->_t("submit")); ?>
                </button>
                <button type="reset" class="btn btn-label-secondary waves-effect" data-bs-dismiss="offcanvas">
                    <?php echo ucfirst($i18n->_t("cancel")); ?>
                </button>
                <input type="hidden">
            </form>
        </div>
    </div>
</div>
<script>
    let userTable;

    document.addEventListener('DOMContentLoaded', function (e) {
        get_data();
    });

    function get_data() {
        fetch('/ajax/users.php')
            .then(response => {
                if (!response.ok) {
                    Swal.fire({
                        title: '<?php echo $i18n->_t("error_title"); ?>',
                        text: '<?php echo $i18n->_t("data_fetch_error"); ?>',
                        icon: 'error',
                        confirmButtonText: '<?php echo $i18n->_t("ok"); ?>'
                    });
                }
                return response.json();
            })
            .then(data => {
                userTable = $('#dt-users').DataTable({
                    data: data['users'],
                    columns: [
                        { data: 'email' },
                        { data: 'phone' },
                        { data: 'fullname' },
                        { data: 'role' },
                        { data: 'status' },
                        {
                            data: 'id',
                            render: function (data, type, row, meta) {
                                return '<button class="btn btn-lg btn-danger" onclick="deleteWords(this,' + data + ');"><?php echo ucfirst($i18n->_t("delete?")); ?></button>'
                            }
                        }
                    ]
                });
                userTable.on('click', 'tbody tr', function (e) {
                    e.currentTarget.classList.toggle('selected');
                });

            });
    }
</script>
<? include ("partial/footer.php");
; ?>