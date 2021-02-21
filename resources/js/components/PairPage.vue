<template>
    <div>
        <div class="col-md-3 mb-3" style="z-index:10">
            <multiselect
                v-model="marketType"
                :options="['binance', 'oil', 'metals', 'others']"
                :multiple="false"
            ></multiselect>
            <br>
            <div v-if="marketType === 'binance'">
                <input type="text" v-model="v1">
                <input type="text" v-model="v2">
                <button @click="go">Go</button>
            </div>
            <div v-else>
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
        <pair
            :cr="cr"
            :s="value"
            :t="marketType"
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
    </div>
</template>

<script>

import Multiselect from "vue-multiselect";

export default {

    props: ["cr", "br", "pr", "tr"],

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
        }
    },

    mounted() {

    },

    methods: {
        getOptions: function() {
            if (this.marketType === "oil") {
                return this.symbols.oil;
            }

            if (this.marketType === "metals") {
                return this.symbols.metals;
            }
        },
        go: function() {
            this.value = (this.v1 + this.v2).toUpperCase();

            this.value = [
                {"name": (this.v1).toUpperCase()},
                {"name": (this.v2).toUpperCase()},
            ]

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
