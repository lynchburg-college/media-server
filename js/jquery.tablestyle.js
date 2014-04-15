
(function($) {
    $.widget("ui.tableStyle", {
        options: {
            altClass: '',
            hoverClass: 'ui-state-highlight'
        },

        _create: function() {
            var self = this;
            self.element.addClass("ui-tableStyle ui-widget ui-widget-content ui-corner-all ui-helper-reset").bind('tablechange.tableStyle', function() {
                _alternateRows();
            }).delegate('tbody > tr', 'mouseenter.tableStyle', function() {
                $(this).addClass(self.options.hoverClass);
            }).delegate('tbody > tr', 'mouseleave.tableStyle', function() {
                $(this).removeClass(self.options.hoverClass);
            });
            self.headers = $(self.element[0].tHead.rows[0].cells);
            self.footer = $(self.element[0].tFoot.rows[0].cells);
            self._initTable();
        },
        _initTable: function() {
            this.headers.addClass("ui-state-default").eq(0).addClass("ui-corner-tl").parent().children().eq(-1).addClass("ui-corner-tr");
            this.footer.addClass("ui-state-default").eq(0).addClass("ui-corner-bl").parent().children().eq(-1).addClass("ui-corner-br");
            this._alternateRows(this.options.altClass);
        },
        _cleanTable: function() {
            this.headers.removeClass("ui-state-default ui-corner-tl ui-corner-tr");
            this.footer.removeClass("ui-state-default");
            this.element.find('.' + this.options.hoverClass + ',.' + this.options.altClass).removeClass(this.options.hoverClass + ' ' + this.options.altClass);
        },
        refresh: function() {
            if (this._trigger("beforerefresh") === false) {
                return;
            };
            this._cleanTable();
            this._initTable();
            this._trigger("afterrefresh");
        },
        destroy: function() {
            this._cleanTable();
            this.element.unbind('.tableStyle');
            this.element.removeClass("ui-tableStyle ui-widget ui-widget-content ui-corner-all").undelegate('tbody > tr', '.tableStyle');
            this.headers.removeClass("ui-state-default");
            this.footer.removeClass("ui-state-default");
            $.Widget.prototype.destroy.apply(this, arguments);
        },
        _setOption: function(key, value) {
            if (key === "altClass") {
                this._trigger('beforerefresh');
                this._cleanTable();
                this.options.altClass = value;
                this._initTable();
                this._trigger("afterrefresh");
            } else if (key === "hoverClass") {
                this._trigger('beforerefresh');
                this._cleanTable();
                this.options.hoverClass = value;
                this._initTable();
                this._trigger("afterrefresh");
            }
            $.Widget.prototype._setOption.apply(this, arguments);
        },
        _alternateRows: function(altClass) {
            if (altClass != '') {
                $(this.element.tBodies).each(function() {
                    $(this.rows).filter(":odd").addClass(altClass);
                });
            }
        }
    });
})(jQuery);

