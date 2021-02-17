<template>
    <div>
        <button @click="transfer(symbol1, symbol2, (1/split) * 100)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol2 }} {{ split }}%</button>
        <button @click="transfer(symbol1, symbol2, 1)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol2 }} 100%</button>
        <button @click="transfer(symbol2, symbol1, (1/split) * 100)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol1 }} {{ split }}%</button>
        <button @click="transfer(symbol2, symbol1, 1)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol1 }} 100%</button>
        <br>
        <button @click="getBalance(symbol1, 'one')" class="btn btn-info mb-2">Balance {{ symbol1 }}: {{ bal.one }} (${{ Math.floor(bal.oneUSD) }})</button>
        <br>
        <button @click="getBalance(symbol2, 'two')" class="btn btn-info mb-2">Balance {{ symbol2 }}: {{ bal.two }} (${{ Math.floor(bal.twoUSD) }})</button>
        <br>
        <button @click="getBalance('USDT', 'usdt')" class="btn btn-info mb-2">Balance USDT: ${{ Math.floor(bal.usdt) }}</button>
    </div>
</template>

<script>
export default {
    props: [
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
            disabled: false
        };
    },

    methods: {
        getBalance: function(symbol, which) {
            let _this = this;
            axios.get(this.cr, {
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
            this.disabled = true;
            let _this = this;
            axios.get("/transfer", {
                params: {
                    from: from,
                    to: to,
                    portion: portion,
                }
            }).then(function () {
               _this.getBalance(from);
               _this.getBalance(to);
               _this.disabled = true;
            });
        },
        inUSD: function(symbol, amount, which) {
            let _this = this;
            axios.get("/price", {
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
