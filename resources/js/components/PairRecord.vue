<template>
    <div class="mr-5 ml-5 mt-5 mb-5 row">
        <div class="col-4">

            <div v-if="data.months" class="mb-3">
                <label class="font-weight-normal mr-3" for="month">Month:</label>
                <select class="form-control" id="month" name="month" v-model="month" @change="getData(s1, s2, month)">
                    <option v-for="month in data.months" :value="month.value">{{ month.name }}</option>
                </select>
            </div>

            <button @click="getData(s1, s2)" class="btn btn-primary">All</button>
            <button @click="getData(s1, s2, month)" class="btn btn-primary">Per Month</button>
            <br>
            <br>
            <table class="table table-bordered table-striped table-hover table-vcenter">
                <thead>
                <tr>
                    <th></th>
                    <th colspan="7">Real</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>If Holding (from time)</th>
                    <th>If $</th>
                </tr>
                <tr>
                    <th></th>
                    <th colspan="2">Balance {{ s1 }}</th>
                    <th colspan="2">Balance {{ s2 }}</th>
                    <th colspan="3">Σ</th>
                    <th></th>
                    <th></th>
<!--                    <th colspan="2">Balance {{ s1 }}</th>-->
<!--                    <th colspan="2">Balance {{ s2 }}</th>-->
                    <th></th>
                    <th>Σ</th>
                    <th>Σ</th>
                </tr>
                <tr>
                    <th>Date</th>
                    <th>{{ s1 }}</th>
                    <th>$</th>
                    <th>{{ s2 }}</th>
                    <th>$</th>
                    <th>In {{ s1 }}</th>
                    <th>In {{ s2 }}</th>
                    <th>$</th>
                    <th>Δ</th>
                    <th>c</th>
                    <th>Δi</th>
<!--                    <th>{{ s1 }}</th>-->
<!--                    <th>$</th>-->
<!--                    <th>{{ s2 }}</th>-->
<!--                    <th>$</th>-->
                    <th>$</th>
                    <th>$</th>
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

                    <td></td>
<!--                    <td>{{ ((item.balance_s1_usd + item.balance_s2_usd) / item.cix).toFixed(2) }}</td>-->

                    <td :class="((item.balance_s1_usd + item.balance_s2_usd) - (item.input_symbol1_usd + item.input_symbol2_usd)) > 0 ? 'bg-success' : 'bg-danger'"
                    >{{ ((item.balance_s1_usd + item.balance_s2_usd) - (item.input_symbol1_usd + item.input_symbol2_usd)).toFixed(2) }}</td>
<!--                    <td>{{ item.input_symbol1.toFixed(2) }}</td>-->
<!--                    <td>{{ item.wbw_usd_1.toFixed(2) }}</td>-->
<!--                    <td>{{ item.input_symbol2.toFixed(2) }}</td>-->
<!--                    <td>{{ item.wbw_usd_2.toFixed(2) }}</td>-->
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
<!--                    <td></td>-->
<!--                    <td></td>-->
<!--                    <td></td>-->
<!--                    <td></td>-->
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
                    <td class="bg-info text-light">{{ ((bals1 * pricec1Now) + (bals2 * pricec2Now)).toFixed(2) }}</td>
                    <td class="text-light"
                        :class="((bals1 * pricec1Now) + (bals2 * pricec2Now)) - ((this.latestRecord.input_symbol1 * pricec1Now) + (this.latestRecord.input_symbol2 * pricec2Now)) > 0 ? 'bg-success' : 'bg-danger'"
                    >
                    {{ (((bals1 * pricec1Now) + (bals2 * pricec2Now)) - ((this.latestRecord.input_symbol1 * pricec1Now) + (this.latestRecord.input_symbol2 * pricec2Now))).toFixed(2) }}
                    </td>
                    <td></td>
<!--                    <td>{{ (((bals1 * pricec1Now) + (bals2 * pricec2Now)) / data.c20_latest).toFixed(2) }}</td>-->
                    <td :class="((bals1 * pricec1Now) + (bals2 * pricec2Now)) - ((this.latestRecord.input_symbol1_usd) + (this.latestRecord.input_symbol2_usd)) > 0 ? 'bg-success' : 'bg-danger'"
                    >
                    {{ (((bals1 * pricec1Now) + (bals2 * pricec2Now)) - ((this.latestRecord.input_symbol1_usd) + (this.latestRecord.input_symbol2_usd))).toFixed(2) }}
                    </td>
<!--                    <td>{{ this.latestRecord.input_symbol1.toFixed(2) }}</td>-->
<!--                    <td>{{ (this.latestRecord.input_symbol1 * pricec1Now).toFixed(2) }}</td>-->
<!--                    <td>{{ this.latestRecord.input_symbol2.toFixed(2) }}</td>-->
<!--                    <td>{{ (this.latestRecord.input_symbol2 * pricec2Now).toFixed(2) }}</td>-->
                    <td class="bg-dark text-light">{{ (this.latestRecord.input_symbol1 * pricec1Now + this.latestRecord.input_symbol2 * pricec2Now).toFixed(2) }}</td>
                    <td class="bg-secondary text-light">{{ (this.latestRecord.input_symbol1_usd + this.latestRecord.input_symbol2_usd).toFixed(2) }}</td>
                </tr>
                </tbody>
            </table>
            <br>
            <button v-if="pricec1" @click="getBalances" class="btn btn-primary">Get Latest</button>
        </div>
    </div>
</template>

<script>
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
            showNewRecord: false,
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
            });
        },
        getBalances: function() {
            let _this = this;

            axios.get(this.br, {
                params: {
                    of: _this.s1,
                }
            }).then(function (response) {
                _this.bals1 = response.data;
            });

            axios.get(this.br, {
                params: {
                    of: _this.s2,
                }
            }).then(function (response) {
                _this.bals2 = response.data;
            });

            axios.get('/latestprices', {
                params: {
                    s1: _this.s1,
                    s2: _this.s2,
                }
            }).then(function (response) {
                _this.pricec1Now = response.data['s1'];
                _this.pricec2Now = response.data['s2'];

                _this.showNewRecord = true;
            });
        },
    },
    computed: {
        latestRecord: function() {
            return Object.values(this.data.records)[Object.keys(this.data.records).length - 1];
        },
    },
    watch: {
        value: function(val) {
            this.showNewRecord = false;
            if (val.length == 2) {
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
