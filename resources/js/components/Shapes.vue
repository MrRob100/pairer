<template>
    <Scene class="sc">
<!--        <Entity :position="[0, 0, 5]">-->
<!--        <Entity v-model="myEntity" :position="[0, 0, 5]">-->
<!--            <Animation propety="rotation.y" :duration="5" :end="Math.PI * 2"></Animation>-->
            <!-- this box will be rotated relative to the parent entity during the animation -->
<!--            <Box></Box>-->
<!--        </Entity>-->

        <Property name="clearColor" color="#FFF"></Property>
        <Camera type="arcRotate"></Camera>
        <HemisphericLight>
            <property name="intensity" :float="2"></property>
        </HemisphericLight>
        <Entity :scaling="[5, 5, 5]">
            <Animation property="rotation.y" :duration="5" :end="-Math.PI * 2"></Animation>
            <Side></Side>
            <Entity :scaling="[-1, 1, 1]">
                <Side></Side>
            </Entity>
        </Entity>
    </Scene>
</template>

<script>

import vb from 'vue-babylonjs';
import Side from './Side';
window.Vue.use(vb);

export default {
    props: ["s1", "s2"],

    components: {Side},

    mounted() {
    },

    methods: {
    },

    watch: {

    }
}

</script>
<style>
    .sc {
        width: 30% !important;
    }
</style>
