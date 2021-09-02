<template>
    <div>
        <button @click="transfer(symbol1, symbol2, (1/splitLow) * 100)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol2 }} 25%</button>
        <button @click="transfer(symbol1, symbol2, (1/split) * 100)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol2 }} {{ split }}%</button>
        <button @click="transfer(symbol1, symbol2, 1)" class="btn btn-info mb-2 mr-1" :disabled="disabled">Buy {{ symbol2 }} 100%</button>
        <button @click="transfer(symbol2, symbol1, (1/splitLow) * 100)" class="btn btn-info mb-2 ml-1" :disabled="disabled">Buy {{ symbol1 }} 25%</button>
        <button @click="transfer(symbol2, symbol1, (1/split) * 100)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol1 }} {{ split }}%</button>
        <button @click="transfer(symbol2, symbol1, 1)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol1 }} 100%</button>
        <br>
        <hr>
        <button @click="getBalance(symbol1, 'one')" class="btn btn-info mb-2">Balance {{ symbol1 }}: {{ bal.one }} (${{ Math.floor(bal.oneUSD) }})</button>
        <br>
        <button @click="getBalance(symbol2, 'two')" class="btn btn-info mb-2">Balance {{ symbol2 }}: {{ bal.two }} (${{ Math.floor(bal.twoUSD) }})</button>
        <br>
        <button @click="getBalance('USDT', 'usdt')" class="btn btn-info mb-2">Balance USDT: ${{ Math.floor(bal.usdt) }}</button>
        <br>
        <button @click="showInputForm()" class="btn btn-info mb-2">Add Input</button>
        <div v-if="showForm" class="mt-3">
            <form v-on:submit.prevent>
                <div class="form-group col-3 pl-0">
                    <input class="form-control mb-2" v-model="input.one" type="number" step="any" :placeholder="'amount ' + symbol1">
                    <input class="form-control mb-2" v-model="input.oneUSD" type="number" step="any" :placeholder="'amount ' + symbol1 + ' usd'">
                    <input class="form-control mb-2" v-model="input.two" type="number" step="any" :placeholder="'amount ' + symbol2">
                    <input class="form-control mb-2" v-model="input.twoUSD" type="number" step="any" :placeholder="'amount ' + symbol2 + ' usd'">
                    <button @click="createInputRecord()" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        "tr",
        "pr",
        "br",
        "cr",
        "symbol1",
        "symbol2",
        "rr",
    ],
    data: function() {
        return {
            splitLow: 25,
            split: 44.5,
            bal: {
                one: null,
                oneUSD: null,
                two: null,
                twoUSD: null,
                usdt: null
            },
            disabled: false,
            showForm: false,
            input: {
                symbol1: null,
                one: null,
                oneUSD: null,
                symbol2: null,
                two: null,
                twoUSD: null,
            }
        };
    },

    methods: {
        createInputRecord: function() {

            this.input.symbol1 = this.symbol1;
            this.input.symbol2 = this.symbol2;

            axios.post(this.rr, this.input).then(() => {
                this.showForm = false;
            }).catch(error => {
                console.error(error);
            })
        },
        showInputForm: function() {
            this.showForm = !this.showForm;
        },
        getBalance: function(symbol, which) {
            let _this = this;
            axios.get(this.br, {
                params: {
                    of: symbol,
                }
            }).then(function (response) {
                _this.bal[which] = response.data;
                if (which !== 'usdt') {
                    _this.inUSD(symbol, response.data, which);
                }
            });
        },
        transfer: function(from, to, portion) {
            if (confirm("Are you sure you want to transfer " + from + " to " + to +"?")) {
                this.disabled = true;
                let _this = this;
                axios.get(this.tr, {
                    params: {
                        from: from.toUpperCase(),
                        to: to.toUpperCase(),
                        portion: portion,
                    }
                }).then(function (response) {
                   _this.getBalance(from);
                   _this.getBalance(to);
                   _this.disabled = !response.data;
                });
            }
        },
        inUSD: function(symbol, amount, which) {
            let _this = this;
            axios.get(this.pr, {
                params: {
                    symbol: symbol,
                }
            }).then(function (response) {
                _this.bal[which + "USD"] = response.data * amount;
            });
        }
    },

    watch: {
        symbol1: function() {
            this.bal.one = "";
            this.bal.oneUSD = "";
        },
        symbol2: function() {
            this.bal.two = "";
            this.bal.twoUSD = "";
        },
    }

}

</script>
