<template>
    <div>
        <div class="col-md-3 mb-3" style="z-index:10">
            <multiselect
                v-model="marketType"
                :options="['cryptos', 'oil', 'metals', 'others']"
                :multiple="false"
            ></multiselect>
            <br>
            <multiselect
                v-model="value"
                :options="getOptions()"
                :multiple="true"
                :max="2"
                label="name"
                track-by="name"
            ></multiselect>
        </div>
        <pair
            :s="value"
            :t="marketType"
        ></pair>
    </div>
</template>

<script>

import Multiselect from "vue-multiselect";
import cryptos from '../../../public/data/cryptos.json';

export default {
    components: {
        Multiselect
    },

    data: function () {
        return {
            value: '',
            symbols: {
                crypto: cryptos,
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
            marketType: "cryptos",
            v1: null,
            v2: null,
        }
    },

    mounted() {
        this.value = [
            { "id": 1, "name": "ZEC"},
            { "id": 2, "name": "ETC"},
        ];
    },

    methods: {
        getOptions: function() {
            if (this.marketType === "cryptos") {
                return this.symbols.crypto;
            }

            if (this.marketType === "oil") {
                return this.symbols.oil;
            }

            if (this.marketType === "metals") {
                return this.symbols.metals;
            }
        }
    },

    watch: {
        marketType: function(val) {
            this.options = this.symbols[val];
        },
        value: function(val) {
            if (val.length == 2) {
                this.v1 = val[0].name;
                this.v2 = val[1].name;
            }
        },
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
