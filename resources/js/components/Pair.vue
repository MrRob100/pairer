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
            <a v-if="this.s.length == 2" :href="this.dlref0" class="btn btn-sm btn-primary m-1"><i class="fa fa-download mr-1"></i>Download csv</a>
        </div>
        <div class="col-4">
            <trading-vuea
                :colorWickUp="lineDataPair ? JSON.stringify(lineDataPair) : null"
                style="z-index: -1"
                colorText="#7DA0B1"
                :data="tradingVueData"
                :overlays="overlays"
                :height="280"
                :width="460"
            ></trading-vuea>
        </div>
        <div class="col-4">
            <trading-vue
                style="z-index: -1"
                colorText="#7DA0B1"
                :data="tradingVue2"
                :height="280"
                :width="460"
            ></trading-vue>

            <a v-if="this.s.length == 2" :href="this.dlref1" class="btn btn-sm btn-primary m-1"><i class="fa fa-download mr-1"></i>Download csv</a>
        </div>
    </div>
</template>
<script>

    import { TradingVue, DataCube } from 'trading-vue-js';
    import Overlays from 'tvjs-overlays';
    import TradingVuea from '../../js/trading-vue-a-2';
    // import TradingVuea from '../../js/trading-vue-a';

    export default {

        components: {
            TradingVue,
            TradingVuea,
        },

        props: {
            cr: "",
            s: "",
            t: "",
            dr: "",
        },

        data: function() {
            return {
                tradingVue1: new DataCube({
                    chart: {
                        data: []
                    },
                }),
                tradingVueData: new DataCube({
                    chart: {
                        data: []
                    },
                    onchart: [
                        {
                            name: 'EMA, 10',
                            type: 'EMA',
                            data: [],
                            settings: {
                                length: 10
                            }
                        }
                    ]
                }),
                tradingVue2: new DataCube({
                    chart: {
                        data: []
                    },
                }),
                overlays: [Overlays['EMA']],
                dlref0: "",
                dlref1: "",
                lineDataPair: [],
            }
        },

        methods: {
            getData: function(s1, s2, t) {
                axios.get(this.cr, {
                    params: {
                        s1,
                        s2,
                        t,
                    }
                }).then(response => {
                    this.tradingVue1.data.chart.data = response.data['first'];

                    this.tradingVueData.data.chart.data = response.data['pair'];
                    this.tradingVueData.data.onchart.data = response.data['pair'];

                    this.tradingVue2.data.chart.data = response.data['second'];

                    this.lineDataPair = response.data['events'];

                    let lasts = [
                        {"s1": response.data['first'][response.data['first'].length - 1][4]},
                        {"s2": response.data['second'][response.data['second'].length - 1][4]},
                    ];

                    this.$emit('lasts', lasts)
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
                    this.dlref0 = this.dr + "?symbol=" + val[0].name.toUpperCase();
                    this.dlref1 = this.dr + "?symbol=" + val[1].name.toUpperCase();
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
