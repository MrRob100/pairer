<template>
    <div>
        <shapes v-if="!mobile" :v1url="v1url" :v2url="v2url"></shapes>
        <div class="row m-2">
            <div class="col-md-3 mb-3" style="z-index:10">
<!--                <multiselect-->
<!--                    v-model="marketType"-->
<!--                    :options="['binance', 'oil', 'metals', 'others', 'iex', 'goldpaxg']"-->
<!--                    :multiple="false"-->
<!--                ></multiselect>-->
                <br>
                <div v-if="marketType === 'binance'">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-8 pr-0">
                                <input type="text" :value="v1.toUpperCase()" @input="v1 = $event.target.value.toUpperCase()" class="form-control mb-1" :disabled="v1frozen">
                            </div>
                            <div class="col-2">
                                <img v-if="v1url" class="coin-icon" :src="v1url">
                            </div>
                            <div class="col-2 pl-1">
                                <button v-if="!v1frozen" @click="freezer" class="btn btn-info" title="Freeze"><i class="fas fa-snowflake text-light"></i></button>
                                <button v-if="v1frozen" @click="freezer" class="btn btn-danger" title="Defrost"><i class="fas fa-snowflake text-light"></i></button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8 mr-0 pr-0">
                                <input type="text" :value="v2.toUpperCase()" @input="v2 = $event.target.value.toUpperCase()" class="form-control">
                            </div>
                            <div class="col-2">
                                <img v-if="v2url" class="coin-icon" :src="v2url">
                            </div>
                        </div>
                    </div>
                    <button @click="go" class="btn btn-success">Go</button>
                    <button @click="randomize" class="btn btn-success"><i class="fa fa-random"></i></button>
                    <button @click="add('active')" class="btn btn-success"><i class="fa fa-bolt"></i></button>
                    <button @click="add('archived')" class="btn btn-secondary"><i class="fa fa-book"></i></button>
                    <button @click="add('next')" class="btn btn-success"><i class="fa fa-lightbulb"></i></button>
                    <button @click="sync" class="btn btn-info"><i class="fa fa-sync"></i></button>
                    <button @click="trash" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                </div>

                <div v-if="marketType === 'goldpaxg'">
                    <button @click="go" class="btn btn-success">Go</button>
                </div>
            </div>
            <div class="col-md-6 pr-0">
                <list
                    @populate="populate"
                    :added="added"
                    :dlr="dlr"
                    :mobile="mobile"
                    :spr="spr"
                ></list>
            </div>
            <div class="col-md-3 pl-0">
                <ul class="nav nav-tabs justify-content-end">
                    <li class="nav-item">
                        <a class="nav-link" href="/results">Results</a>
                    </li>
                </ul>
            </div>
        </div>
        <pair
            :cr="cr"
            :dr="dr"
            :stopLimitSellPrice="stopLimitSellPrice"
            :stopLimitSellPriceFloating="stopLimitSellPriceFloating"
            :limitBuyPrice="limitBuyPrice"
            :limitBuyPriceFloating="limitBuyPriceFloating"
            :mobile="mobile"
            @pure="setPure"
            :s="value"
            :t="marketType"
            @lasts="sendLasts"
        ></pair>
        <br>
        <br>
        <div class="container">
<!--            <input type="number" class="input" :value="lastCombined">-->
<!--            <br>-->
<!--            <br>-->
            <controls
                :canGetLimits="changeToGetLimits"
                :symbol1="v1.toUpperCase()"
                :symbol2="v2.toUpperCase()"
                :cr="cr"
                :br="br"
                :pr="pr"
                @limitBuyPrice="setLimitBuyPrice"
                @limitBuyPriceFloating="setLimitBuyPriceFloating"
                @stopLimitSellPrice="setStopLimitSellPrice"
                @stopLimitSellPriceFloating="setStopLimitSellPriceFloating"
                :pure="pure"
                :tr="tr"
                :rr="rr"
                :shaver="shaver"
                :pumpr="pumpr"
                :push-lasts="pushLasts"
            >
            </controls>
        </div>
        <pair-record
            :br="br"
            :mobile="mobile"
            :push-lasts="pushLasts"
            :value="value">
        </pair-record>
<!--        <record :bdr="bdr"></record>-->
    </div>
</template>

<script>

import Multiselect from "vue-multiselect";

export default {

    props: [
        "mobile",
        "cr",
        "br",
        "pr",
        "tr",
        "spr",
        "cpr",
        "dlr",
        "bdr",
        "dr",
        "rand",
        "dp",
        "rr",
        "shaver",
        "pumpr",
        "icon",
    ],

    components: {
        Multiselect
    },

    data: function () {
        return {
            changeToGetLimits: null,
            limitBuyPrice: null,
            limitBuyPriceFloating: null,
            stopLimitSellPrice: null,
            stopLimitSellPriceFloating: null,
            value: "",
            symbols: {
                oil: [
                    { "id": 1, "name": "USO"},
                    { "id": 2, "name": "WTI"},
                    { "id": 3, "name": "HAL"},
                    { "id": 4, "name": "BKR"},
                    { "id": 5, "name": "CLB"},
                ],
                metals: [
                    { "id": 1, "name": "SILVER"},
                    { "id": 2, "name": "XAUUSD"},
                    { "id": 3, "name": "XAGUSD"},
                    { "id": 3, "name": "PAXG"},
                ],
                others: [
                    { "id": 1, "name": "NASDAQ"},
                    { "id": 2, "name": "US30"},
                    { "id": 3, "name": "SPX"},
                    { "id": 4, "name": "GS"},
                ]
            },
            marketType: "binance",
            pure: false,
            v1: "",
            v2: "",
            v1url: null,
            v2url: null,
            v1frozen: false,
            added: [],
            pushLasts: [],
        }
    },

    methods: {
        freezer: function() {
            this.v1frozen = !this.v1frozen;
        },
        sendLasts: function(data) {
            this.pushLasts = data;
        },
        setPure: function(val) {
            this.pure = val;
            this.changeToGetLimits = (new Date()).getTime();
        },
        setLimitBuyPrice: function(val) {
            this.limitBuyPrice = val;
        },
        setStopLimitSellPrice: function(val) {
            this.stopLimitSellPrice = val;
        },
        setLimitBuyPriceFloating: function(val) {
            this.limitBuyPriceFloating = val;
        },
        setStopLimitSellPriceFloating: function(val) {
            this.stopLimitSellPriceFloating = val;
        },
        getOptions: function() {
            if (this.marketType === "oil") {
                return this.symbols.oil;
            }

            if (this.marketType === "metals") {
                return this.symbols.metals;
            }
        },
        go: function() {

            if (this.marketType === "goldpaxg") {
                this.v1 = "gold";
                this.v2 = "paxg";
            }

            this.value = [
                {"name": (this.v1).toUpperCase()},
                {"name": (this.v2).toUpperCase()},
            ]

        },
        populate: function(s1, s2) {
            this.v1 = s1;
            this.v2 = s2;

            this.go();
        },

        sync: function() {
            axios.get('sync');
        },

        add: function(state) {
            let _this = this;
            axios
                .post(this.cpr, {
                    params: {
                        s1: this.v1.toUpperCase(),
                        s2: this.v2.toUpperCase(),
                        state: state,
                    },
                })
                .then(function() {
                    _this.added = [
                        {"s1": _this.v1},
                        {"s2": _this.v2}
                    ]
                });
        },

        randomize: function() {
            let _this = this;
            axios.get(this.rand).then(response => {
                _this.v2 = response.data.v2;

                _this.value = [
                    {"name": _this.v1},
                    {"name": (response.data.v2).toUpperCase()},
                ]

                if (!this.v1frozen) {
                    _this.v1 = response.data.v1;
                    _this.value = [
                        {"name": (response.data.v1).toUpperCase()},
                        {"name": (response.data.v2).toUpperCase()},
                    ]
                }
            });
        },
        trash: function() {
            let _this = this;
            axios.post(this.dp, {
                params: {
                    s1: this.v1,
                    s2: this.v2,
                }
            }).then(response => {
                if (!this.v1frozen) {
                    _this.v1 = '';
                }
                _this.v2 = '';
            })
        }
    },

    watch: {
        marketType: function(val) {
            this.options = this.symbols[val];
        },
        v1: function(val) {
            axios.get(this.icon, {params: {symbol: val}}).then(response => this.v1url = response.data);
        },
        v2: function(val) {
            axios.get(this.icon, {params: {symbol: val}}).then(response => this.v2url = response.data);
        }
    },
    computed: {
        lastCombined: function() {
            if (this.pushLasts.length > 0) {
                return this.pushLasts[0].s1 / this.pushLasts[1].s2;
            } else {
                return null;
            }
        }
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
