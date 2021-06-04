<template>
    <div>
        <div class="row m-2">
            <div class="col-md-3 mb-3" style="z-index:10">
                <multiselect
                    v-model="marketType"
                    :options="['binance', 'oil', 'metals', 'others', 'iex', 'goldpaxg']"
                    :multiple="false"
                ></multiselect>
                <br>
                <div v-if="marketType === 'binance'">
                    <div class="form-group">
                        <input type="text" v-model="v1" class="form-control mb-1">
                        <input type="text" v-model="v2" class="form-control">
                    </div>
                    <button @click="go" class="btn btn-success">Go</button>
                    <button @click="add" class="btn btn-success"><i class="fa fa-plus"></i></button>
                    <button @click="randomize" class="btn btn-success"><i class="fa fa-random"></i></button>
                    <button @click="trash" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                </div>

                <div v-if="marketType === 'iex'">
                    <div class="form-group">
                        <input type="text" v-model="v1" class="form-control mb-1">
                        <input type="text" v-model="v2" class="form-control">
                    </div>
                    <button @click="go" class="btn btn-success">Go</button>
                </div>

                <div v-if="marketType === 'goldpaxg'">
                    <button @click="go" class="btn btn-success">Go</button>
                </div>

                <div v-if="marketType === 'oil'">
                    <multiselect
                        v-model="value"
                        :options="getOptions()"
                        :multiple="true"
                        :max="2"
                        label="name"
                        track-by="name"
                    ></multiselect>
                </div>
            </div>
            <div class="col-md-9">
                <list
                    @populate="populate"
                    :spr="spr"
                    :added="added"
                    :dlr="dlr"
                ></list>
            </div>
        </div>
        <pair
            :cr="cr"
            :s="value"
            :t="marketType"
            :dr="dr"
            @lasts="sendLasts"
        ></pair>
        <br>
        <div class="container">
            <controls
                :symbol1="v1.toUpperCase()"
                :symbol2="v2.toUpperCase()"
                :cr="cr"
                :br="br"
                :pr="pr"
                :tr="tr">
            </controls>
<!--            <limits></limits>-->
        </div>
        <pair-record :value="value" :push-lasts="pushLasts"></pair-record>
<!--        <record :bdr="bdr"></record>-->
    </div>
</template>

<script>

import Multiselect from "vue-multiselect";

export default {

    props: ["cr", "br", "pr", "tr", "spr", "cpr", "dlr", "bdr", "dr", "rand", "dp"],

    components: {
        Multiselect
    },

    data: function () {
        return {
            value: '',
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
            v1: "",
            v2: "",
            added: [],
            pushLasts: [],
        }
    },

    methods: {
        sendLasts: function(data) {
            this.pushLasts = data;
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

        add: function() {
            let _this = this;
            axios
                .post(this.cpr, {
                    params: {
                        s1: this.v1.toUpperCase(),
                        s2: this.v2.toUpperCase(),
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
                _this.v1 = response.data.v1;
                _this.v2 = response.data.v2;

                _this.value = [
                    {"name": (response.data.v1).toUpperCase()},
                    {"name": (response.data.v2).toUpperCase()},
                ]
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
                _this.v1 = '';
                _this.v2 = '';
            })
        }
    },

    watch: {
        marketType: function(val) {
            this.options = this.symbols[val];
        }
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
