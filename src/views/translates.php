<?
include ("partial/header.php");
?>
<p>Bu sayfaya eklenecekler;</p>
<p>Toplu seçim işlemleri : <br>
    - Silme <br>
    - Başka dil için toplu kopyalama <br>
    - Dil değiştirme : Başka dilde yoksa kabul eder, var ise hata verir değiştirmez <br>
    <br>
    - Import words : Buton olacak .json dosya kabul edecek. ([{block,value,language}]) <br>
    - Export words : Buton olacak .json çıktı verecek. ([{block,value,language}]) <br>
    <br>

</p>
<div class="nav-align-top">
    <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
        <li class="nav-item" role="presentation">
            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#exist" aria-controls="exist" aria-selected="true" id="exist-tab">
                <i class="tf-icons ti ti-brackets-contain ti-xs me-1"></i> <? echo ucfirst_tr($i18n->_t("exist")); ?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button type="button" class="nav-link disabled" role="tab" data-bs-toggle="tab" data-bs-target="#news" aria-controls="news" aria-selected="false" tabindex="-1" id="news-link">
                <i class="tf-icons ti ti-pencil-plus ti-xs me-1"></i> <? echo ucfirst_tr($i18n->_t("new_added")); ?>
                <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-danger ms-1" id="news_count"></span>
            </button>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="exist" role="tabpanel">
            <table id="dt-i18n-words" class="table table-100">
                <thead>
                    <tr>
                        <th><? echo ucfirst_tr($i18n->_t("block")); ?></th>
                        <th><? echo ucfirst_tr($i18n->_t("value")); ?></th>
                        <th><? echo ucfirst_tr($i18n->_t("language")); ?></th>
                        <th><? echo ucfirst_tr($i18n->_t("actions")); ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="news" role="tabpanel">
            <table id="dt-i18n-new-words" class="table table-100">
                <thead>
                    <tr>
                        <th><? echo ucfirst_tr($i18n->_t("block")); ?></th>
                        <th><? echo ucfirst_tr($i18n->_t("value")); ?></th>
                        <th><? echo ucfirst_tr($i18n->_t("language")); ?></th>
                        <th><? echo ucfirst_tr($i18n->_t("actions")); ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    var news_table;
    var exist_table;

    document.addEventListener('DOMContentLoaded', function (e) {
        get_data();
    });

    function get_data() {
        fetch('/ajax/translates.php')
            .then(response => {
                if (!response.ok) {
                    Swal.fire({
                        title: '<?php echo ucfirst_tr($i18n->_t("error_title")); ?>',
                        text: '<?php echo ucfirst_tr($i18n->_t("data_fetch_error")); ?>',
                        icon: 'error',
                        confirmButtonText: '<?php echo ucfirst_tr($i18n->_t("ok")); ?>'
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data['new'].length == 0) { } else {
                    $('#news_count').html(`${data['new'].length}`);
                    news_table = $('#dt-i18n-new-words').DataTable({
                        ordering: false,
                        data: data['new'],
                        columns: [
                            { data: 'block' },
                            {
                                data: 'value',
                                render: function (data, type, row, meta) {
                                    return '<input type="text" style="width:100%;" class="form-control" data-id="' + row['id'] + '" value="' + data + '" onclick="this.select();" onchange="translate_isoK(this,true);" />';
                                }
                            },
                            { data: 'language' },
                            {
                                data: 'id',
                                render: function (data, type, row, meta) {
                                    return '<button class="btn btn-lg btn-danger" style="width:100%;" onclick="deleteWords(this,' + data + ');"><?php echo ucfirst_tr($i18n->_t("delete?")); ?></button>'
                                }
                            }
                        ]
                    });
                    news_table.on('click', 'tbody tr', function (e) {
                        e.currentTarget.classList.toggle('selected');
                    });
                    $('#news-link').removeClass("disabled");
                }

                exist_table = $('#dt-i18n-words').DataTable({
                    ordering: false,
                    select: true,
                    data: data['exist'],
                    columns: [
                        { data: 'block' },
                        {
                            data: 'value',
                            render: function (data, type, row, meta) {
                                return '<input type="text" style="width:100%;" class="form-control" data-id="' + row['id'] + '" value="' + data + '" onclick="this.select();" onchange="translate_isoK(this,false);" />';
                            }
                        },
                        { data: 'language' },
                        {
                            data: 'id',
                            render: function (data, type, row, meta) {
                                return '<button class="btn btn-lg btn-danger" style="width:100%;" onclick="deleteWords(this,' + data + ');"><?php echo ucfirst_tr($i18n->_t("delete?")); ?></button>'
                            }
                        }
                    ],
                    initComplete: function () {
                        this.api().columns().every(function () {
                            var column = this;
                            if (!column.header().innerHTML.trim().includes("<?php echo ucfirst_tr($i18n->_t("actions")); ?>")) {
                                var select = $('<select class="form-control"><option value=""></option></select>')
                                    .appendTo($(column.header()))
                                    .on('change', function () {
                                        var val = $.fn.dataTable.util.escapeRegex(
                                            $(this).val()
                                        );

                                        column
                                            .search(val ? '^' + val + '$' : '', true, false)
                                            .draw();
                                    });

                                column.data().unique().sort().each(function (d, j) {
                                    select.append('<option value="' + d + '">' + d + '</option>')
                                });
                            }

                        });
                    }
                });

                exist_table.on('click', 'tbody tr', function (e) {
                    e.currentTarget.classList.toggle('selected');
                });
            })
            .catch(error => {
                Swal.fire({
                    title: '<?php echo ucfirst_tr($i18n->_t("error_title")); ?>',
                    text: '<?php echo ucfirst_tr($i18n->_t("data_fetch_error")); ?>',
                    icon: 'error',
                    confirmButtonText: '<?php echo ucfirst_tr($i18n->_t("ok")); ?>'
                });
            });
    }

    function translate_isoK(element, isdelete) {
        const data = {
            id: element.getAttribute("data-id"),
            value: element.value
        };

        fetch("/ajax/translates.php", {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => {
                if (!response.ok) {
                    Swal.fire({
                        title: '<?php echo ucfirst_tr($i18n->_t("error_title")); ?>',
                        text: '<?php echo ucfirst_tr($i18n->_t("data_fetch_error")); ?>',
                        icon: 'error',
                        confirmButtonText: '<?php echo ucfirst_tr($i18n->_t("ok")); ?>'
                    });
                }
                return response.json();
            })
            .then(data => {
                if (isdelete) {
                    var deleted_element = element.parentNode.parentElement;
                    deleted_element.id = "deleted_element";
                    $(`#deleted_element`).css("background-color", "green");
                    $(`#deleted_element`)
                        .fadeOut(250, function () {
                            $(this).remove();
                        });
                    var news_count = parseInt($('#news_count').html());
                    news_count = news_count - 1
                    if (news_count == 0) {
                        $('#news-link').addClass("disabled");
                        $('#exist-tab').tab('show');
                    }
                    $('#news_count').html(`${news_count == 0 ? '' : news_count}`);
                }
                toastMixin.fire({
                    animation: true,
                    title: '<?php echo ucfirst_tr($i18n->_t("i18n_successfully_updated")); ?>'
                });
            })
            .catch(error => {
                Swal.fire({
                    title: '<?php echo ucfirst_tr($i18n->_t("error_title")); ?>',
                    text: `${error}`,
                    icon: 'error',
                    confirmButtonText: '<?php echo ucfirst_tr($i18n->_t("ok")); ?>'
                });
            });
    }

    function deleteWords(element, idd) {

        fetch(`/ajax/translates.php?id=${idd}`, {
            method: 'DELETE'
        })
            .then(response => {
                if (!response.ok) {
                    Swal.fire({
                        title: '<?php echo ucfirst_tr($i18n->_t("error_title")); ?>',
                        text: '<?php echo ucfirst_tr($i18n->_t("data_fetch_error")); ?>',
                        icon: 'error',
                        confirmButtonText: '<?php echo ucfirst_tr($i18n->_t("ok")); ?>'
                    });
                }
                return response.json();
            })
            .then(data => {
                var deleted_element = element.parentNode.parentElement;
                deleted_element.id = "deleted_element";
                $(`#deleted_element`).css("background-color", "green");
                $(`#deleted_element`)
                    .fadeOut(250, function () {
                        $(this).remove();
                    });

                toastMixin.fire({
                    animation: true,
                    title: '<?php echo ucfirst_tr($i18n->_t("i18n_successfully_deleted")); ?>'
                });


                get_data();
            })
            .catch(error => {
                Swal.fire({
                    title: '<?php echo ucfirst_tr($i18n->_t("error_title")); ?>',
                    text: `${error}`,
                    icon: 'error',
                    confirmButtonText: '<?php echo ucfirst_tr($i18n->_t("ok")); ?>'
                });
            });
    }


</script>

<?
include ("partial/footer.php");
?>