(function() {
    var menu = document.getElementById('menu');
    var uls = menu.getElementsByTagName('ul');
    for (var i = 0, c = uls.length; i < c; ++i) {
        uls[i].style.height = 0;
        var triangle = uls[i].parentElement.firstElementChild.lastElementChild;
        setRotateValue(triangle, -90);

        var a = uls[i].parentElement.firstElementChild;
        a.onclick = function() {
            var ul = this.parentElement.lastElementChild;
            var triangle = this.parentElement.firstElementChild.lastElementChild;
            if (ul.clientHeight === 0) {
                animateRotate(triangle, 0);
                animateShow(ul);
            } else {
                animateRotate(triangle, -90);
                animateHide(ul);
            }
        }
    }

    function setRotateValue(elem, value) {
        if (!elem) return;
        // INFO: rotate работает только на блочных элементах
        value = "rotate(" + value + "deg)";
        // INFO: кроссбраузерность css свойств кажется поддерживается так:
        elem.style.transform = value;
        elem.style.webkitTransform = value;
        // INFO: даже oTransform это нормально
    }
    function getRotateValue(elem) {
        if (!elem) return;
        var value = elem.style.transform || elem.style.webkitTransform;
        return +value.split('(')[1].split('deg')[0];
    }

    function increaseUlHeight(ul, diffHeight) {
        if (ul.nodeName == "UL" && ul.className != "menu") {
            ul.style.height = (ul.clientHeight + diffHeight) + "px";
            increaseUlHeight(ul.parentElement.parentElement, diffHeight);
        }
    }

    function animateHide(el) {
        if (el.clientHeight != 0) {
            var oldHeight = el.clientHeight;
            var newHeight = el.clientHeight - 6;
            if (newHeight < 0) newHeight = 0;
            increaseUlHeight(el, newHeight - oldHeight);
            // INFO: () => {} работает не во всех браузерах
            setTimeout(function(){ animateHide(el) }, 1);
        }
    }

    function animateShow(el) {
        el.style.height = "";
        var stopHeight = el.clientHeight;
        el.style.height = 0;
        show();

        function show() {
            if (el.clientHeight != stopHeight) {
                var oldHeight = el.clientHeight;
                var newHeight = el.clientHeight + 6;
                if (newHeight > stopHeight) newHeight = stopHeight;
                increaseUlHeight(el, newHeight - oldHeight);
                setTimeout(function() { show() }, 1);
            }
        }
    }

    function animateRotate(elem, deg) {
        var currentDeg = getRotateValue(elem);
        var diff = 3;
        var timeout = 1;
        if (deg > currentDeg) {
            var newDeg = currentDeg + diff;
            if (newDeg > deg) newDeg = deg;
            setRotateValue(elem, newDeg);
            setTimeout(function(){ animateRotate(elem, deg) }, timeout);
        } else if (deg < currentDeg) {
            var newDeg = currentDeg - diff;
            if (newDeg < deg) newDeg = deg;
            setRotateValue(elem, newDeg);
            setTimeout(function(){ animateRotate(elem, deg) }, timeout);
        }
    }
})();