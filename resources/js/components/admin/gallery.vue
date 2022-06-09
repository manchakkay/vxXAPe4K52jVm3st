<template>
    <img
        class="image-preview"
        loading="eager"
        v-for="(item, index) in this.items"
        :key="index"
        :src="item.src"
        v-show="index == this.active"
    />
    <div class="gallery-button prev" @click="this.prev()">
        <slot name="svg-button-prev"></slot>
    </div>
    <div class="gallery-button next" @click="this.next()">
        <slot name="svg-button-next"></slot>
    </div>
    <div class="gallery-counter">
        {{ this.active + 1 + " / " + this.items.length }}
    </div>
</template>


<script>
export default {
    props: {
        files: {},
    },
    data: () => ({
        active: 0,
        items: [],
    }),
    beforeMount() {
        JSON.parse(this.files).forEach((file) => {
            this.items.push({
                src: file["url"],
            });
        });
    },
    methods: {
        next: function () {
            this.active =
                this.active+1 < this.items.length ? this.active + 1 : this.active;
        },
        prev: function () {
            this.active = this.active > 0 ? this.active - 1 : this.active;
        },
    },
};
</script>
