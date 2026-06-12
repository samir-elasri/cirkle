import $ from "jquery";

export default ($component, elements, attributes, properties) => {
    const {
        availableTabs,
    } = properties;
    let tabs = $('.tabs .tab', $component);
    let defaultTab = 'supplier';
    let originTab = new URLSearchParams(window.location.search).get('tab') ?? defaultTab;
    let originSubTab = new URLSearchParams(window.location.search).get('subTab');


    function init() {
        tabs.click(function () {
            let tabId = $(this).data('tab');
            setTabActive(tabId);
        });

        $('.nested-tabs .nested-tab').click(function () {
            let subTabId = $(this).data('tab');
            setSubTabActive(subTabId);
        });
    }

    function handleTabId(tabId) {
        let nestedTabs = $(`.nested-tab`);
        nestedTabs.removeClass('active-tab active');

        if (availableTabs.hasOwnProperty(tabId)) {
            availableTabs[tabId].forEach((tab) => {
                $(`[data-tab="${tab}"]`, $component).addClass('active-tab');
            });
        }

        let subTab = $('.active-tab', $component).first();
        subTab.addClass('active');
        setParams('tab', tabId);
        setParams('subTab', subTab.data('tab'));

    }

    function setTabActive(tabId) {
        tabs.removeClass('active');
        let target = $(`.tab-item[data-tab="${tabId}"]`);
        if (target.length === 0) {
            target = $(`.tab-item[data-tab]`).first();
            originSubTab = null;
            tabId = target.data('tab');
        }
        target.addClass('active');
        handleTabId(tabId);
        $('.tabcontent', $component).removeClass('active');
        $('.tabcontent[data-tab="' + tabId + '"]', $component).addClass('active');
        setSubTabActive(originSubTab ?? $('.active-tab', $component).first().data('tab'));
    }

    function setSubTabActive(tabId) {
        let subtab = $(`.nested-tab[data-tab="${tabId}"]`, $component);
        if (!$(`.nested-tab[data-tab="${tabId}"]`, $component).hasClass('active-tab')) {
            subtab = $('.nested-tab.active-tab', $component).first();
            tabId = subtab.data('tab');
        }
        subtab.siblings().removeClass('active');
        subtab.addClass('active');
        subtab.parent().siblings('.nested-tabcontent').removeClass('active');
        subtab.parent().siblings('.nested-tabcontent[data-tab="' + tabId + '"]').addClass('active');
        setParams('subTab', tabId);
    }

    function setParams(prop, value) {
        let params = new URLSearchParams(window.location.search);
        let location = new URL(window.location);
        params.set(prop, value);
        location.search = params;
        window.history.pushState('', '', location);
    }

    $(document).ready(function () {
        setTabActive(originTab ?? 'client');
        originSubTab = null;
        init();
    });
}