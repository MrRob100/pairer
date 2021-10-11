<template>
    <Scene>
        <Camera></Camera>
        <HemisphericLight diffuse="#0000FF"></HemisphericLight>
        <Entity :position="[-3, 0, 5]">
            <Animation property="rotation.y" :duration="13" :end="Math.PI * 2"></Animation>
            <Animation property="rotation.x" :duration="10" :end="Math.PI * 2"></Animation>
            <PointLight diffuse="#FF0000"></PointLight>
            <Cylinder>
                <property name="scaling" :vector="{ x: 5, y: 0.5, z: 5 }"></property>
                <Material emissive="#FFF">
                    <Texture :src="v1url ? v1url : 'https://picsum.photos/id/' + (Math.random() * 100).toFixed() + '/200/300'"></Texture>
                </Material>
            </Cylinder>
        </Entity>
        <Entity :position="[3, 0, 5]">
            <Animation property="rotation.y" :duration="10" :end="Math.PI * 2"></Animation>
            <Animation property="rotation.x" :duration="15" :end="Math.PI * 2"></Animation>
            <PointLight diffuse="#FF0000"></PointLight>
            <Cylinder>
                <property name="scaling" :vector="{ x: 5, y: 0.5, z: 5 }"></property>
                <Material emissive="#FFF">
                    <Texture :src="v2url ? v2url : 'https://picsum.photos/id/' + (Math.random() * 100).toFixed() + '/200/300'"></Texture>
                </Material>
            </Cylinder>
        </Entity>
    </Scene>
</template>

<script>
export default {
    name: "App",
    props: ["v1url", "v2url"],
    data() {
        return { testScene: null, testCylinder: null };
    },
};
</script>

<style>
#cont {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
    height: 100%;
    width: 50%;
}
</style>
