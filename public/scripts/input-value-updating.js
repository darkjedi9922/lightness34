function setUpdatingInputValueFromInput(fromId, whereId) {
    from = document.getElementById(fromId);
    where = document.getElementById(whereId);
    from.onchange = function() {
        value = from.value.split('\\')[2];
        if (value) where.value = value;
        else where.value = '';
    }
}