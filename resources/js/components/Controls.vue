<template>
    <div>
        <button @click="transfer(symbol1, symbol2, (1/split) * 100)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol2 }} {{ split }}%</button>
        <button @click="transfer(symbol1, symbol2, 1)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol2 }} 100%</button>
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
        <div v-if="showForm">
            <input type="text" :value="symbol1">
            <input type="number" :placeholder="'amount ' + symbol1">
            <input type="number" placeholder="amount $">
            <input type="text" :value="symbol2">
            <input type="number" :placeholder="'amount ' + symbol2">
            <input type="number" placeholder="amount $">
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
        "symbol2"
    ],
    data: function() {
        return {
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
        };
    },

    methods: {
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
