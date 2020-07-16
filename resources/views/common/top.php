<div class="top-bar">
    {% if name() %}
    <button class="btn btn-light top-bar-item btn-member-setting">{{ name() }}</button>
    {% endif %}
</div>
{% if name() %}
<div class="sidebar-wrapper">
    <div class="sidebar-top">
        <h4 class="close-sidebar">×</h4>
    </div>
    <div class="name-wrapper"><h5 class="name">{{ name() }}</h5></div>
    <div class="bottom-line"></div>
    <div class="list-wrapper">
        <ul class="list">
            <li><a href="{{ pathFor('SignOut') }}"><i class="fa fa-sign-out"></i>&nbsp; 登出</a></li>
        </ul>
    </div>
    <div class="bottom-line"></div>
    <input name="types" type="hidden" value="{{ typesOfMember }}">
    <div id="place_type_list" class="place-type-list"></div>
</div>
{% endif %}