<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add Food
            </h2>
        </template>
        <div>
            <form method="POST" @submit.prevent="submit">
                <div class="grid grid-cols-12 gap-2">
                    <p class="col-span-12 text-red-700 font-bold" v-if="errors.description">{{errors.description}}</p>
                    <label class="py-2 col-span-2" for="description">Description:</label>
                    <input class="border rounded col-span-10" id="description" type="text" v-model="food.description">
                    <p class="col-span-12" v-if="errors.alias">{{errors.alias}}</p>
                    <label class="py-2 col-span-2" for="alias">Alias:</label>
                    <input class="border rounded col-span-2" id="alias" type="text" v-model="food.alias"/>
                    <p class="col-span-8"></p>
                    <p class="col-span-12" v-if="errors.kcal">{{errors.kcal}}</p>
                    <label class="py-2 col-span-2" for="KCal">KCal:</label>
                    <input class="border rounded col-span-2" id="kcal" type="number" v-model="food.kcal" min="0"/>
                    <p class="col-span-8"></p>
                    <p class="col-span-4" v-if="errors.protein">{{errors.protein}}</p>
                    <label class="py-2 col-span-2" for="Protein">Protein:</label>
                    <input class="border rounded col-span-2" id="protein" type="number" v-model="food.protein" min="0"/>
                    <p class="col-span-8"></p>
                    <p class="col-span-12" v-if="errors.fat">{{errors.fat}}</p>
                    <label class="py-2 col-span-2" for="Fat">Fat:</label>
                    <input class="border rounded col-span-2" id="fat" type="number" v-model="food.fat" min="0"/>
                    <p class="col-span-8"></p>
                    <p class="col-span-12" v-if="errors.carbohydrate">{{errors.carbohydrate}}</p>
                    <label class="py-2 col-span-2" for="Carbohydrate">Carbohydrate:</label>
                    <input class="border rounded col-span-2" id="carbohydrate" type="number" v-model="food.carbohydrate" min="0"/>
                    <p class="col-span-8"></p>
                    <p class="col-span-12" v-if="errors.potassium">{{errors.potassium}}</p>
                    <label class="py-2 col-span-2" for="Potassium">Potassium:</label>
                    <input class="border rounded col-span-2" id="potassium" type="number" v-model="food.potassium" min="0"/>
                    <p class="col-span-8"></p>
                    <p class="col-span-12" v-if="errors.base_quantity">{{errors.base_quantity}}</p>
                    <label class="py-2 col-span-2" for="Quantity">Quantity:</label>
                    <input class="border rounded col-span-2" id="base_quantity" type="number" v-model="food.base_quantity" min="0"/>
                    <p class="col-span-8"></p>
                </div>
            </form>
            <div class="leading-10 grid grid-cols-4 mt-4">
                <div class="col-span-1 flex justify-between">
                    <button class="border rounded px-4" @click="store">Save</button>
                    <button class="border rounded px-4" @click="cancel">Cancel</button>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>

import AppLayout from '@/Layouts/AppLayout';

export default {
    components:{
        AppLayout,
    },
    props:{
        errors: Object,
        user: Object,
    },
    data() {
        return {
            food:{
                description: '',
                alias: '',
                kcal: 0,
                fat: 0,
                protein: 0,
                carbohydrate: 0,
                potassium: 0,
                favourite: true,
                base_quantity: 0,
                foodsource_id: 2,
                foodgroup_id: 26,
                user_id: this.user.id
            }
        }
    },
    methods: {
        store(){
            this.$inertia.post(
                this.$route("foods.store"), this.food
            );
        },
        cancel () {
            let url = `${this.$route("foods.index")}`;
            this.$inertia.visit(url);
        },
    }
}
</script>
