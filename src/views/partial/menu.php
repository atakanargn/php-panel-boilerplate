<li class="menu-item<? echo $sayfa == "index" ? " active" : "" ?>">
    <a href="/" class="menu-link">
        <i class="menu-icon tf-icons ti ti-smart-home"></i>
        <div><? echo ucfirst_tr(_t("index")); ?></div>
    </a>
</li>
<li class="menu-item<? echo $sayfa == "users" ? " active" : "" ?>">
    <a href="/users" class="menu-link">
        <i class="menu-icon tf-icons ti ti-users"></i>
        <div><? echo ucfirst_tr(_t("users")); ?></div>
    </a>
</li>
<li class="menu-item<? echo $sayfa == "translate" ? " active" : "" ?>">
    <a href="/translate" class="menu-link">
        <i class="menu-icon tf-icons ti ti-language"></i>
        <div><? echo ucfirst_tr(_t("translates")); ?></div>
    </a>
</li>