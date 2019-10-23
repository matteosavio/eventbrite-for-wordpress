function showAllEvents() {
    var HiddenElements = document.getElementsByClassName('hidden event');
    for (var i = 0, max = HiddenElements.length; i < max; i++) {
        HiddenElements[i].style.display = 'grid';
    }
    document.querySelector('.moreevents').style.display = 'none';
}