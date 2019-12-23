<li class="message ${type} {{if read == '0'}}no_read{{/if}}">
    <div class="chat_avatar">
        <img class="avatar" src="${avatar}">
        <span style="display: block;text-align: center">${other.time_text}</span>
    </div>
    <div class="chat_msg">
        <div class="box arrow_box">
            <h6>${name}</h6>
            <p>{{html message}}</p>
        </div>
    </div>
</li>