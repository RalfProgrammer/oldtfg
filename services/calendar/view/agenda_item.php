{{if user_rol == 1}}
    <li name="${data.id}">
        <input type="hidden" name="start_time" value="${data.other.start_time}">
        <div class="event_item box">
            <div class="event_date" style="padding: 3px 10px">
                <h6>${data.other.month}</h6>
                <h3>${data.other.day}</h3>
                <h7>${data.other.year}</h7>
            </div>
            <div class="event_description">
                <span>${data.request}</span>
                <span>${data.other.hour} {{if data.online}}Online{{else}}en ${data.location}{{/if}} , ${data.other.doctor} </span>
            </div>
        </div>
    </li>
{{else}}
    <li name="${data.id}">
        <input type="hidden" name="start_time" value="${data.other.start_time}">
        <div class="event_item box">
            <div class="event_date">
                <h3>${data.other.hour.split(':')[0]}</h6>
                <h6>${data.other.hour.split(':')[1]}</h6>
            </div>
            <div class="event_description">
                <span>${data.request}</span>
                <span>${data.other.patient} {{if data.online}}Online{{else}}en ${data.location}{{/if}} </span>
            </div>
        </div>
    </li>
{{/if}}