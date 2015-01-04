/**
 * Created by Katonchik on 26.12.2014.
 */

function Calendar(containerElement, locale) {
    var container           = containerElement,
        currentMonthStart   = new Date(),
        daysThisMonth,
        monthStartDaySys,
        monthStartDayOfWeek,
        daysBeforeStart;

    var self = this;

    /**
     * Initializes month variables; this function is called with every month change.
     */
    function initializeMonth() {
        self.currentMonth   = currentMonthStart.getMonth();
        self.currentYear    = currentMonthStart.getFullYear();
        if(typeof(monthNames) !== 'undefined') {
            self.monthName = monthNames[self.currentMonth];
        }
        else {
            self.monthName = defaultMonthNames[self.currentMonth];
        }
        daysThisMonth       = new Date(self.currentYear, self.currentMonth+1, 0).getDate();
        monthStartDaySys    = currentMonthStart.getDay();
        monthStartDayOfWeek = (monthStartDaySys == 0 ? 7 : monthStartDaySys);
        daysBeforeStart     = monthStartDayOfWeek - 1;

        container.addEventListener('click', function(ev) {
            if (ev.target.classList && (ev.target.classList.contains("calendar__cell--weekend")
                || ev.target.classList.contains("calendar__cell--unavailable"))) {
                console.log("weekend clicked");
                var eventDate = new Date(self.currentYear, self.currentMonth, ev.target.id);
                if(ev.target.classList.contains("calendar__cell--weekend")) {
                    var eventName = prompt("Please enter event name, e.g. ЗЧУ", "");
                    if(eventName){
                        addCalendarEvent(eventDate, eventName);
                    }
                }
                else {
                    var confirmMsg = confirm("Remove event?");
                    if(confirmMsg){
                        removeCalendarEvent(eventDate);
                    }
                }
            }
        });
    }

    /**
     * Draws month calendar, including the title, scroll controls and the dates grid
     */
    function render(){
        drawScrollControl('&lt;&lt;', 'goToPrev');
        renderMonthTitle();
        drawScrollControl('&gt;&gt;', 'goToNext');

        var i;
        var calDate = new Date(currentMonthStart.getTime());
        //alert("inside draw" + container);
        for(i=0; i<daysBeforeStart; i++)
        {
            renderCell(null);
        }
        for(i=1; i<daysThisMonth+1; i++)
        {
            calDate.setDate(i);
            renderCell(calDate);
        }
        var remainingEmpty = (Math.floor((daysBeforeStart + daysThisMonth - 1)/7) + 1) * 7 - (daysBeforeStart + daysThisMonth);
        for(i=0; i<remainingEmpty; i++)
        {
            renderCell(null);
        }
    }

    /**
     * Draws scroll control, either 'prev' or 'next'
     * @param label Whatever text shows on the scroll control
     * @param controlID HTML element ID of the scroll control
     */
    function drawScrollControl(label, controlID) {
        var scrollControl = document.createElement('div');
        scrollControl.innerHTML = label;
        scrollControl.classList.add("calendar__scrollControl");
        scrollControl.id = controlID;
        scrollControl.addEventListener("click", changeMonth, false);
        container.appendChild(scrollControl);
    }

    /**
     * Draws calendar title, including the name of the month and the year
     */
    function renderMonthTitle() {
        var titleDiv   = document.createElement('div');
        titleDiv.innerHTML = self.monthName + " " + self.currentYear;
        titleDiv.classList.add('calendar__monthTitle');
        container.appendChild(titleDiv);
    }

    /**
     * Draws individual cells
     * @param calDate Date object
     */
    function renderCell(calDate) {
        var dateCell = document.createElement('div');
        container.appendChild(dateCell);
        dateCell.classList.add("calendar__cell");

        if(calDate === null) {
            dateCell.classList.add('calendar__cell--empty');
            return;
        }

        var dayOfMonth    = calDate.getDate(),
            dayOfMonthStr = '' + dayOfMonth,
            dayOfWeek     = calDate.getDay();
        if(dayOfWeek == 0 || dayOfWeek == 6) {
            dateCell.classList.add('calendar__cell--weekend');
        }
        else{
            dateCell.classList.add('calendar__cell--weekday');
        }
        dateCell.id = dayOfMonthStr;
        dateCell.innerHTML = dayOfMonthStr;
    }

    /**
     * Changes current month. This function is triggered on month scroll.
     */
    function changeMonth(){
        if(this.id == 'goToPrev'){
            currentMonthStart.setMonth(self.currentMonth - 1 );
        }
        else {
            currentMonthStart.setMonth(self.currentMonth + 1 );
        }

        initializeMonth();
        refresh();
    }

    /**
     * Removes old month's HTML elements and draws current month's calendar.
     */
    function refresh(){
        while (container.firstChild) {
            container.removeChild(container.firstChild);
        }
        render();
    }

    var defaultMonthNames = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];

    this.goTo = function(date){
        currentMonthStart = date;
        currentMonthStart.setDate(1);
        initializeMonth();
        refresh();
    };

    function addCalendarEvent(eventDate, eventName){

        console.log("doing addCalendarEvent: " + eventName);
        var dateStr = getDateStr(eventDate);

        $.ajax({
            url: 'calendar_ajax.php',
            data: {
                'eventDate' : dateStr,
                'eventName' : eventName,
                'action'    : 'add'
            },
            dataType: 'json',
            success: function(data){

                if(data.successful)
                {
                    var dayOfMonth = eventDate.getDate(),
                        dayElement = document.getElementById('' + dayOfMonth);
                    dayElement.classList.remove("calendar__cell--weekend");
                    dayElement.classList.add("calendar__cell--unavailable");
                    dayElement.innerHTML += " - " + eventName;
                    console.log(eventName + " added to the calendar");
                }
                else
                {
                    console.log("Failed to add " + eventName + " to the calendar: " + data.msg);
                }

            },
            error: function(data){
                console.log("Failed to add " + eventName + " to the calendar");
            }
        });

        return false;

    }

    function removeCalendarEvent(eventDate){

        var dateStr = getDateStr(eventDate);
        $.ajax({
            url: 'calendar_ajax.php',
            data: {
                'eventDate' : dateStr,
                'action'    : 'remove'
            },
            dataType: 'json',
            success: function(data){

                if(data.successful)
                {
                    console.log("Event removed from the calendar");
                    var dayOfMonth = eventDate.getDate(),
                        dayElement = document.getElementById('' + dayOfMonth);
                    dayElement.classList.remove("calendar__cell--unavailable");
                    dayElement.classList.add("calendar__cell--weekend");
                    dayElement.innerHTML = dayElement.id;
                }
                else
                {
                    console.log("Failed to remove event from the calendar: " + data.msg);
                }

            },
            error: function(data){
                console.log("Failed to remove event from the calendar");
            }
        });

        return false;

    }

    function getDateStr(date){
        var monthStr = self.currentMonth + 1,
            dateStr = date.getDate();
        return self.currentYear + "_" + monthStr + "_" + dateStr;
    }

    currentMonthStart.setDate(1);
    initializeMonth();
    render();

}