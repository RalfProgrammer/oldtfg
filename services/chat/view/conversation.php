<li class="chat_list_conversation" name="${user_B}">
    <div class="box row">
        <div class="ch_con_avatar">
            <img src="${other.avatar}" class="avatar big">
        </div>
        <div class="row">
            <h5 class="ch_con_user">${other.fullname}<small class="ch_con_time">${other.time}</small></h5>
            <span class="n_new_messages {{if other.num_msg > 0}}on{{/if}}">${other.num_msg}</span><span class="ch_con_message">${other.last_msg}</span>
        </div>
    </div>
</li>