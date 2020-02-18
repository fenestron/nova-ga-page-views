<template>
    <card class="px-4 py-4">
        <div class="mb-4">
            <h3 class="mr-3 text-base text-80 font-bold">{{ this.title }}</h3>
        </div>
        <div v-if="!pages" class="flex items-center">
            <p class="text-80 font-bold">Page list not found</p>
        </div>
        <ul v-else class="most-visited-pages-list mb-4 mt-2 overflow-y-scroll" style="list-style: none; padding: 0;">
            <li v-for="page in pages" style="margin: 2px 0;">
                <a :href="`https://${page.hostname}${page.path}`" target="_blank">{{ page.name }}</a>: {{ page.visits }}
            </li>
        </ul>
    </card>
</template>

<script>
    export default {
        props: ['card'],

        data: function() {
            return {
                pages: [],
                title: '',
            }
        },

        mounted() {
            if (typeof this.card.category === 'undefined') {
                alert('Category for GetPageViews does not exist');
                return;
            }

            const category = this.card.category;

            Nova.request().get('/nova-vendor/page-views-list?category=' + category).then(response => {
                this.pages = response.data.pages;
                this.title = response.data.title;
            });
        },
    }
</script>

<style scoped>
    .most-visited-pages-list {
        height: 4.6rem;
    }
</style>
