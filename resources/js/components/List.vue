<template>
    <div>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">Active</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Archive</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Next</a>
            </li>
        </ul>
        <ul class="col-list">
            <li v-for="pair in pairs">
                <p @click="populate(pair.s1, pair.s2)" class="btn-link mr-2">{{ (pair.s1 + pair.s2).toUpperCase() }}</p>
                <i @click="deletePair(pair.id)" class="fa fa-times grey-cross"></i>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: [
        "added",
        "spr",
        "dlr",
        "cpr",
    ],
    data: function() {
        return {
            pairs: [],
            totalPairs: [],
        };
    },

    mounted() {
        this.getSavedPairs();
    },

    methods: {
        getSavedPairs() {
            axios
                .get(this.spr)
                .then((response) => (this.pairs = response.data))
                .catch((error) => console.log(error));
        },

        populate(s1, s2) {
            this.$emit('populate', s1, s2);
        },

        deletePair(id) {
            let _this = this;
            axios
                .post(this.dlr, {
                    params: {
                        id: id,
                    },
                })
                .then(function() {
                    _this.getSavedPairs();
                });
        }
    },

    watch: {
        added: function() {
            this.getSavedPairs();
        }
    }

}

</script>
<style lang="scss">
.col-list {
    cursor: pointer;
    columns: 3;
    -webkit-columns: 3;
    -moz-columns: 3;

    p {
        display: contents;
        font-size: 20px;
        font-family: monospace;
    }

    li {
        list-style-type: none;
    }

    i {
        cursor: pointer;
    }

    .grey-cross {
        color: #777;
    }
}
</style>
