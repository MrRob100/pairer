## Trading-vue hacks

Removing top on label (formerly ohclv data)

    ...
    12008     calc_style: function calc_style() {
    12008         var top = this.layout.height > 150 ? 10 : 5;
    12008         return {
    12008             // top: "".concat(this.layout.offset + top, "px")
    12008         };
    12008     },
    ....


Wick piggy back

    ....
    9590 onwards


Price label colour unset

    9605

Price line events

    ....
    5166 onwards
