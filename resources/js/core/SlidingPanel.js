import $ from 'jquery';

// =======================================================
// SlidingPanel
// =======================================================
function SlidingPanel(_container, _options) {
    this.container = _container;
    this.options = _options;
    this.bg = _options.bg;
    this.togglesOpen = $(_options.togglesOpen).map((index, element) => {
        return element.toArray();
    });
    this.togglesClose = $(_options.togglesClose).map((index, element) => {
        return element.toArray();
    });
    this.togglesOpen.on('click', this.onClickToOpen.bind(this));
    this.togglesClose.on('click', this.onClickToClose.bind(this));
    SlidingPanel.allPanels.push(this);
}

// Static variables
SlidingPanel.allPanels = [];

SlidingPanel.prototype = {

    onClickToOpen(e){
        if(this.open()){
            e.preventDefault();
            e.stopPropagation();
        }
    },

    onClickToClose(e){
        if(this.close()){
            e.preventDefault();
            e.stopPropagation();
        }
    },

    open(timescale) {
        if(!this.container.is(':visible')){
            if(typeof timescale === 'undefined') timescale = 1;
            for(let i = 0, l = SlidingPanel.allPanels.length; i < l; i++){
                SlidingPanel.allPanels[i].close();
            }
            this.container.stop().show().css({left: "-100%"}).animate({left: "0px"}, 500 * timescale);
            this.bg.stop().show().css({opacity: 0}).animate({opacity: 0.5}, 500 * timescale);

            if(typeof this.options.openCallback === 'function'){
                this.options.openCallback();
            }
            return true;
        }
        return false;
    },

    close(timescale) {
        const self = this;
        if(this.container.is(':visible')){
            if(typeof timescale === 'undefined') timescale = 1;
            this.container.stop().css({left: "0px"}).animate({left: "-100%"}, 350 * timescale, () => {
                self.container.hide();
            });
            this.bg.stop().show().css({opacity: 0.5}).animate({opacity: 0}, 350 * timescale, () => {
                self.bg.hide();
            });
            return true;
        }
        return false;
    }
};

export default SlidingPanel;
