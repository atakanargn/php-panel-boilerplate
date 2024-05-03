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
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#new" aria-controls="new" aria-selected="false" tabindex="-1">
                        <i class="tf-icons ti ti-pencil-plus ti-xs me-1"></i> <? echo ucfirst(_t("new_added")); ?>
                        <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-danger ms-1">3</span>
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
                    <p>
                        Oat cake chupa chups dragée donut toffee. Sweet cotton candy jelly beans macaroon gummies
                        cupcake gummi bears cake chocolate.
                    </p>
                    <p class="mb-0">
                        Cake chocolate bar cotton candy apple pie tootsie roll ice cream apple pie brownie cake. Sweet
                        roll icing sesame snaps caramels danish toffee. Brownie biscuit dessert dessert. Pudding jelly
                        jelly-o tart brownie jelly.
                    </p>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function (e) {

            });
        </script>

        <?
        include ("partial/footer.php");
}
?>