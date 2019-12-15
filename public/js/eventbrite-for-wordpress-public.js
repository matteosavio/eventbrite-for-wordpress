function showAllEvents(locationSelector = null) {
    if(locationSelector) {
        var Events = document.getElementsByClassName('event');
        for (var i = 0, max = Events.length; i < max; i++) {
            if (Events[i].classList.contains(locationSelector)) {
                Events[i].style.display = 'grid';
            }
            else {
                Events[i].style.display = 'none';
            }
            jump('#events');
        }
    }
    else {
        var Events = document.getElementsByClassName('event');
        for (var i = 0, max = Events.length; i < max; i++) {
            if(Events[i].style.display != 'grid') {
                Events[i].style.display = 'grid';
            }
        }
    }
    
    function jump(h){
        var url = location.href;               //Save down the URL without hash.
        location.href = "#"+h;                 //Go to the target element.
        history.replaceState(null,null,url);   //Don't like hashes. Changing it back.
    }
}