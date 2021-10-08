<template>
    <div class="mr-5 ml-5 mt-5 mb-5 row">
        <div class="col-10 m-auto">
            <div v-if="data.months" class="mb-3">
                <label class="font-weight-normal mr-3" for="month">Month:</label>
                <select class="form-control col-3" id="month" name="month" v-model="month" @change="getData(s1, s2, month)">
                    <option v-for="month in data.months" :value="month.value">{{ month.name }}</option>
                </select>
            </div>

            <button @click="getData(s1, s2)" class="btn btn-primary">All</button>
            <button @click="getData(s1, s2, month)" class="btn btn-primary">Per Month</button>
            <div id="performance_container">
            </div>
        </div>
        <div class="col-10 m-auto">
            <table v-if="data.length != 0" class="table table-bordered table-striped table-hover table-vcenter">
                <thead>
                <tr>
                    <th></th>
                    <th class="text-center" colspan="7">Real</th>
                    <th class="text-center" colspan="2"></th>
                    <th class="text-center">If Holding (from time)</th>
                    <th class="text-center">If $</th>
                </tr>
                <tr>
                    <th></th>
                    <th class="text-center" colspan="2">Balance {{ s1 }}</th>
                    <th class="text-center" colspan="2">Balance {{ s2 }}</th>
                    <th class="text-center" colspan="3">Σ</th>
                    <th class="text-center" colspan="2">Δ</th>
                    <th class="text-center">Σ</th>
                    <th class="text-center">Σ</th>
                </tr>
                <tr>
                    <th class="text-center">Date</th>
                    <th class="text-center">{{ s1 }}</th>
                    <th class="text-center">$</th>
                    <th class="text-center">{{ s2 }}</th>
                    <th class="text-center">$</th>
                    <th class="text-center">In {{ s1 }}</th>
                    <th class="text-center">In {{ s2 }}</th>
                    <th class="text-center">$</th>
                    <th class="text-center">Δ</th>
                    <th class="text-center">Δi</th>
                    <th class="text-center">$</th>
                    <th class="text-center">$</th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="item in data.records">
                        <td>{{ formatDate(item.created_at) }}</td>
                        <td>{{ item.balance_s1.toFixed(2) }}</td>
                        <td>{{ item.balance_s1_usd.toFixed(2) }}</td>
                        <td>{{ item.balance_s2.toFixed(2) }}</td>
                        <td>{{ item.balance_s2_usd.toFixed(2) }}</td>
                        <td class="bg-secondary">{{ ((item.balance_s1_usd + item.balance_s2_usd) / item.price_at_trade_s1).toFixed(2) }}</td>
                        <td class="bg-secondary">{{ ((item.balance_s1_usd + item.balance_s2_usd) / item.price_at_trade_s2).toFixed(2) }}</td>
                        <td class="bg-info text-light">{{ (item.balance_s1_usd + item.balance_s2_usd).toFixed(2) }}</td>

                        <td class="text-light" :class="((item.balance_s1_usd + item.balance_s2_usd) - (item.wbw_usd_1 + item.wbw_usd_2)) > 0 ? 'bg-success' : 'bg-danger'"
                        >{{ ((item.balance_s1_usd + item.balance_s2_usd) - (item.wbw_usd_1 + item.wbw_usd_2)).toFixed(2) }}</td>

                        <td :class="((item.balance_s1_usd + item.balance_s2_usd) - (item.input_symbol1_usd + item.input_symbol2_usd)) > 0 ? 'bg-success' : 'bg-danger'"
                        >{{ ((item.balance_s1_usd + item.balance_s2_usd) - (item.input_symbol1_usd + item.input_symbol2_usd)).toFixed(2) }}</td>
                        <td class="bg-dark text-light">{{ (item.wbw_usd_1 + item.wbw_usd_2).toFixed(2) }}</td>
                        <td class="bg-secondary text-light">{{ (item.input_symbol1_usd + item.input_symbol2_usd).toFixed(2) }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr v-if="((bals1 && bals2) || (bals1 && bals2 == 0) || (bals2 && bals1 == 0)) && (this.latestRecord) && showNewRecord">
                        <td>{{ formatDate(new Date()) }}</td>
                        <td>{{ bals1.toFixed(2) }}</td>
                        <td>{{ (bals1 * pricec1Now).toFixed(2) }}</td>
                        <td>{{ bals2.toFixed(2) }}</td>
                        <td>{{ (bals2 * pricec2Now).toFixed(2) }}</td>
                        <td class="bg-secondary">{{ (((bals1 * pricec1Now) + (bals2 * pricec2Now)) / pricec1Now).toFixed(2) }}</td>
                        <td class="bg-secondary">{{ (((bals1 * pricec1Now) + (bals2 * pricec2Now)) / pricec2Now).toFixed(2) }}</td>
                        <td class="bg-info text-light">{{ currentValue }}</td>
                        <td class="text-light"
                            :class="(((bals1 * pricec1Now) + (bals2 * pricec2Now)) - ((this.latestRecord.input_symbol1 + this.latestInput.s1.s1) * pricec1Now + (this.latestRecord.input_symbol2 + this.latestInput.s2.s2) * pricec2Now)) > 0 ? 'bg-success' : 'bg-danger'"
                        >
                        {{ (((bals1 * pricec1Now) + (bals2 * pricec2Now)) - ((this.latestRecord.input_symbol1 + this.latestInput.s1.s1) * pricec1Now + (this.latestRecord.input_symbol2 + this.latestInput.s2.s2) * pricec2Now)).toFixed(2) }}
                        </td>
                        <td :class="((bals1 * pricec1Now) + (bals2 * pricec2Now)) - (this.totalInput) > 0 ? 'bg-success' : 'bg-danger'"
                        >
                        {{ (((bals1 * pricec1Now) + (bals2 * pricec2Now)) - (this.totalInput)).toFixed(2) }}
                        </td>
                        <td class="bg-dark text-light">{{ ((this.latestRecord.input_symbol1 + this.latestInput.s1.s1) * pricec1Now + (this.latestRecord.input_symbol2 + this.latestInput.s2.s2) * pricec2Now).toFixed(2) }}</td>
                        <td class="bg-secondary text-light">{{ (this.totalInput).toFixed(2) }}</td>
                    </tr>
                </tbody>
            </table>
            <br>
            <button v-if="pricec1" @click="getBalances" class="btn btn-primary">Get Latest</button>
        </div>
    </div>
</template>

<script>
import Chart from 'chart.js';

export default {
    props: [
        "value",
        "push-lasts",
        "br"
    ],
    data: function() {
        return {
            month: null,
            data: [],
            pricec1: null,
            pricec2: null,
            s1: null,
            s2: null,
            bals1: null,
            bals2: null,
            pricec1Now: null,
            pricec2Now: null,
            latestInput: {
                s1: {
                    s1: null,
                    usd: null,
                },
                s2: {
                    s2: null,
                    usd: null,
                },
            },
            showNewRecord: false,
            latest: false,
            graphData: {
                type: "bar",
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: "Pair value if holding",
                            type: "line",
                            data: [],
                            backgroundColor: "rgba(54,73,93,.5)",
                            borderColor: "#36495d",
                            borderWidth: 3
                        },
                        {
                            label: "Pair Value",
                            type: "line",
                            data: [],
                            backgroundColor: "rgba(71, 183,132,.5)",
                            borderColor: "#47b784",
                            borderWidth: 3
                        },
                        {
                            label: "Input",
                            type: "bar",
                            data: [],
                            backgroundColor: "rgba(71,127,183,0.5)",
                            borderColor: "#4779b7",
                            borderWidth: 3
                        }
                    ]
                }
            }
        };
    },

    methods: {
        formatDate: function(date) {
            var obj = new Date(date);
            return obj.toLocaleDateString();
        },
        getData: function(s1, s2, month = null) {
            this.s1 = s1;
            this.s2 = s2;
            axios.get('/get_pair_data', {
                params: {
                    s1,
                    s2,
                    month,
                }
            }).then(response => {
                this.data = response.data;
                this.month = response.data.current_month;
                this.formatChartData(response.data);
            });
        },

        formatChartData: function(data) {
            let labels = [];
            let value = [];
            let valueIfHolding = [];
            let inputs = [];

            Object.values(data.records).forEach((item) => {
                labels.push(item.created_at.substring(0, 10));
                value.push(item.balance_s1_usd + item.balance_s2_usd);
                valueIfHolding.push(item.wbw_usd_1 + item.wbw_usd_2);
                inputs.push(item.input_symbol1_usd + item.input_symbol2_usd);
            });

            this.graphData.data.labels = labels;
            this.graphData.data.datasets[0].data = valueIfHolding;
            this.graphData.data.datasets[1].data = value;
            this.graphData.data.datasets[2].data = inputs;

            this.newChart();
        },
        pushLatestToChart: function() {
            let today = new Date();

            if (this.latest) {
                let offset = this.graphData.data.datasets[0].data.length - 1;

                this.graphData.data.labels[offset] = today.toISOString().split('T')[0];
                this.graphData.data.datasets[0].data[offset] = this.valueIfHolding;
                this.graphData.data.datasets[1].data[offset] = this.currentValue;
                this.graphData.data.datasets[2].data[offset] = this.totalInput.toFixed(2);
            } else {
                this.graphData.data.labels.push(today.toISOString().split('T')[0]);
                this.graphData.data.datasets[0].data.push(this.valueIfHolding);
                this.graphData.data.datasets[1].data.push(this.currentValue);
                this.graphData.data.datasets[2].data.push((this.totalInput).toFixed(2));
            }

            this.newChart();
            this.latest = true;
        },
        newChart: function() {
            let oldCanvasContainer =  document.getElementById('performance_container');

            if (typeof(oldCanvasContainer) != 'undefined' && oldCanvasContainer != null) {
                oldCanvasContainer.innerHTML = '';
            }

            let canvasContainer = document.getElementById('performance_container');
            let canvas = document.createElement('canvas');
            canvasContainer.appendChild(canvas);

            new Chart(canvas, this.graphData);
        },
        getBalances: function() {
            let _this = this;
            _this.showNewRecord = true;

            axios.get(this.br, {
                params: {
                    of: _this.s1,
                }
            }).then(function (response) {
                _this.bals1 = response.data;
                _this.pushLatestToChart();
            });

            axios.get(this.br, {
                params: {
                    of: _this.s2,
                }
            }).then(function (response) {
                _this.bals2 = response.data;
                _this.pushLatestToChart();
            });

            axios.get('/latestprices', {
                params: {
                    s1: _this.s1,
                    s2: _this.s2,
                }
            }).then(function (response) {
                _this.pricec1Now = response.data['s1'];
                _this.pricec2Now = response.data['s2'];

                if (response.data['latest_input']) {
                    _this.latestInput.s1.usd = response.data['latest_input']['amount1_usd'];
                    _this.latestInput.s1.s1 = response.data['latest_input']['amount1'];
                    _this.latestInput.s2.usd = response.data['latest_input']['amount2_usd'];
                    _this.latestInput.s2.s2 = response.data['latest_input']['amount2'];
                } else {
                    _this.latestInput.s1.usd = null;
                    _this.latestInput.s1.s1 = null;
                    _this.latestInput.s2.usd = null;
                    _this.latestInput.s2.s2 = null;
                }
                _this.pushLatestToChart();
                _this.showNewRecord = true;
            });
        },
    },
    computed: {
        latestRecord: function() {
            return Object.values(this.data.records)[Object.keys(this.data.records).length - 1];
        },
        latestInputTotal: function() {
            return this.latestInput.s1.usd + this.latestInput.s2.usd;
        },
        totalInput: function() {
            return this.latestRecord.input_symbol1_usd + this.latestRecord.input_symbol2_usd + this.latestInputTotal;
        },
        currentValue: function() {
            return ((this.bals1 * this.pricec1Now) + (this.bals2 * this.pricec2Now)).toFixed(2);
        },
        valueIfHolding: function() {
            return ((this.latestRecord.input_symbol1 + this.latestInput.s1.s1) * this.pricec1Now + (this.latestRecord.input_symbol2 + this.latestInput.s2.s2) * this.pricec2Now).toFixed(2)  ;
        },
    },
    watch: {
        value: function(val) {
            if (val.length == 2) {
                this.showNewRecord = false;
                this.latest = false;
                this.getData(val[0].name, val[1].name);
            }
        },
        pushLasts: function(val) {
            this.pricec1 = val[0].s1;
            this.pricec2 = val[1].s2;
        }
    }
}

</script>
