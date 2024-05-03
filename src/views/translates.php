<?
if ($method == "PUT") {

} else if ($method == "GET") {
    include ("partial/header.php");
    ?>
        <div class="nav-align-top mb-4">
            <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#exist" aria-controls="exist" aria-selected="true">
                        <i class="tf-icons ti ti-brackets-contain ti-xs me-1"></i> <? echo ucfirst(_t("exist")); ?>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link disabled" role="tab" data-bs-toggle="tab" data-bs-target="#new" aria-controls="new" aria-selected="false" tabindex="-1" id="news">
                        <i class="tf-icons ti ti-pencil-plus ti-xs me-1"></i> <? echo ucfirst(_t("new_added")); ?>
                        <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-danger ms-1" id="news_count"></span>
                    </button>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show" id="exist" role="tabpanel">
                    <table id="dt-i18n-words" class="table dataTable no-footer dtr-column">
                        <thead>
                            <tr>
                                <th><? echo ucfirst(_t("block")); ?></th>
                                <th><? echo ucfirst(_t("value")); ?></th>
                                <th><? echo ucfirst(_t("language")); ?></th>
                                <th><? echo ucfirst(_t("actions")); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="new" role="tabpanel">
                    <table id="dt-i18n-new-words" class="table dataTable no-footer dtr-column">
                        <thead>
                            <tr>
                                <th><? echo ucfirst(_t("block")); ?></th>
                                <th><? echo ucfirst(_t("value")); ?></th>
                                <th><? echo ucfirst(_t("language")); ?></th>
                                <th><? echo ucfirst(_t("actions")); ?></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function (e) {
                fetch('/ajax/translates.php')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data['new'].length == 0) {

                        } else {
                            $('#news_count').html(`${data['new'].length}`);
                            var table = $('#dt-i18n-new-words').DataTable({
                                responsive: true,
                                data: data['new'],
                                columns: [
                                    { data: 'block' },
                                    {
                                        data: 'value',
                                        render: function (data, type, row, meta) {
                                            return '<input type="text" style="width:100%;" class="form-control" data-id="' + row['id'] + '" value="' + data + '">';
                                        }
                                    },
                                    { data: 'language' },
                                    {
                                        data: 'id',
                                        render: function (data, type, row, meta) {
                                            return 'ISLEM'
                                        }
                                    }
                                ]
                            });
                            $('#news').removeClass("disabled");
                        }
                        console.log(data);
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                    });

            });
        </script>

        <?
        include ("partial/footer.php");
}
?>