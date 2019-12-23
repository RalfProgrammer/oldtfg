var Popup = function(){

    this._vars = {
        id      : Math.floor(Math.random() * 1000),
        wrapper : false,
        modal   : false
    }

    var top = $('body').scrollTop() + 50;

    this._vars.wrapper =
        $('<div class="popup" name="' + this._vars.id + '" style="top: ' + top +'px;">' +
            '<div class="popupHeader blue">' +
                '<span>Titulo</span>'+
            '</div>' +
            '<div class="popupWrapper"></div>' +
          '</div>');

    var content = false;

    this._vars.wrapper.find('.popupHeader').append('<nav class="closePopup"><i class="fa fa-times"></i></nav>');
    this._vars.wrapper.find('.popupWrapper').addLoader();

    $('body').append(this._vars.wrapper);

    this.setModal = function(on){
        if(on){
            this._vars.modal = $('<div class="popup_modal" name="' + this._vars.id  +'"></div>');
            $('body').append(this._vars.modal);
        }else{
            if(this._vars.modal){
                this._vars.modal.remove();
                this._vars.modal = false;
            }
        }
    }

    this.setContent = function(content){
        this.getContent().html(content);
        return this.getContent();
    }

    this.getContent = function(){
        return this._vars.wrapper.find('.popupWrapper');
    }

    this.setTitle = function(text){
        this._vars.wrapper.find('.popupHeader span').text(text);
    }

    this.setMaxSize = function(value){
        this._vars.wrapper.css('max-width', value + 'px');
    }

    this.minimize = function(){
        this._vars.wrapper.addClass('min');
    }

    this.show = function(){
        this._vars.wrapper.show();
        if(this._vars.modal)
            this._vars.modal.show();
    }

    this.hide = function(){
        this._vars.wrapper.hide();
        if(this._vars.modal)
            this._vars.modal.hide();
    }

    this.close = function(){
        this._vars.wrapper.remove();
        if(this._vars.modal)
            this._vars.modal.remove();

    }

    this.setCaller = function(text){
        this._vars.wrapper.find('h2').text(text);
    }

    if(content)
        this.setContent(content);

    this._vars.wrapper.find('nav.closePopup').click(function(){
        var popup_id = $(this).parents('.popup').attr('name');
        $('.popup[name=' + popup_id + ']').remove();
        $('.popup_modal[name=' + popup_id + ']').remove();
    })
}