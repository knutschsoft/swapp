module.exports = function(selectClass, hrefClass) {
    $(function () {
        var selectElement = window.document.getElementsByClassName(selectClass)[0];

        if (selectElement === undefined) {
            return;
        }

        selectElement.addEventListener('change', function () {
            var href, hrefParts, linkElem;
            linkElem = window.document.getElementsByClassName(hrefClass)[0];
            href = linkElem.getAttribute('href');
            hrefParts = href.split('/');
            hrefParts.pop();
            hrefParts.push(this.value);
            href = hrefParts.join('/');
            linkElem.setAttribute('href', href);
        });
    });
};
