<template>
    <div class="row">
        <div class="col-4">
            <trading-vue
                style="z-index: -1"
                colorText="#7DA0B1"
                :data="tradingVue1"
                :height="280"
                :width="460"
            ></trading-vue>
        </div>
        <div class="col-4">
            <trading-vue
                style="z-index: -1"
                colorText="#7DA0B1"
                :data="tradingVueData"
                :height="280"
                :width="460"
            ></trading-vue>
        </div>
        <div class="col-4">
            <trading-vue
                style="z-index: -1"
                colorText="#7DA0B1"
                :data="tradingVue2"
                :height="280"
                :width="460"
            ></trading-vue>
        </div>
    </div>
</template>
<script>

    import TradingVue from 'trading-vue-js'

    export default {

        components: {
            TradingVue,
        },

        props: {
            s: "",
            t: "",
        },

        data: function() {
            return {
                tradingVue1: {
                    ohlcv: [],
                },
                tradingVueData: {
                    ohlcv: [],
                },
                tradingVue2: {
                    ohlcv: [],
                },
            }
        },

        methods: {
            getData: function(s1, s2, t) {
                axios.get('/chart', {
                    params: {
                        s1,
                        s2,
                        t,
                    }
                }).then(response => {
                    this.tradingVue1.ohlcv = response.data['first'];
                    this.tradingVueData.ohlcv = response.data['pair'];
                    this.tradingVue2.ohlcv = response.data['second'];
                });
            },
            setChartHeading: function(val) {
                var elems = document.getElementsByClassName('trading-vue-ohlcv');

                elems[0].style.display = "block";
                elems[0].innerText = val[0].name + "USD";

                elems[1].style.display = "block";
                elems[1].innerText = val[0].name + val[1].name;

                elems[2].style.display = "block";
                elems[2].innerText = val[1].name + "USD";
            }
        },

        watch: {
            s: function(val) {
                if (val.length == 2) {
                    this.getData(val[0].name, val[1].name, this.t);
                    this.setChartHeading(val);
                }
            },
        }
    }
</script>
<style lang="scss">
.trading-vue-legend {
    z-index: 1;
    font-size: 1.5rem;
}
.trading-vue-ohlcv {
    display: none;
}
</style>
