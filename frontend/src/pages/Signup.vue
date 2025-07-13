<script setup>

import Guest from "../components/Guest.vue";
import {ref} from "vue";
import axiosClient from "../axios.js";
import router from "../router.js";

const data = ref({
  name: '',
  email: '',    
  password: '',
  password_confirmation: ''
});
const errors = ref({});

function submit() {
  axiosClient.get('/sanctum/csrf-cookie').then(response => {
    axiosClient.post("/api/register", data.value)
        .then(response => {
          router.push({name: 'Home'})
        })
        .catch(error => {
          console.log(error.response.data)
          errors.value = error.response.data.errors;
        })
  });
}

</script>

<template>
    <Guest>
        <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Create A New Account</h2>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">

          <form @submit.prevent="submit" class="space-y-6">

                <div>
                    <label for="name" class="block text-sm/6 font-medium text-gray-900">Name</label>
                    <div class="mt-2">
                        <input type="name"
                         name="name"
                          id="name"
                            v-model="data.name"
                           required=""
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm/6 font-medium text-gray-900">Email address</label>
                    <div class="mt-2">
                        <input
                         type="email"
                          name="email"
                           id="email"
                            v-model="data.email"
                            required=""
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm/6 font-medium text-gray-900">Password</label>

                    </div>
                    <div class="mt-2">
                        <input
                         type="password" 
                         name="password"
                          id="password"
                            v-model="data.password"
                           required=""
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="confirm-password" class="block text-sm/6 font-medium text-gray-900">Confirm
                            Password</label>

                    </div>
                    <div class="mt-2">
                         <input type="password" name="password_confirmation" id="password_confirmation" required
              v-model="data.password_confirmation"
                           
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                    </div>
                </div>



                <div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Sign
                        up</button>
                </div>
            </form>

            <p class="mt-10 text-center text-sm/6 text-gray-500">
                You already have an account?
                {{ ' ' }}
                <router-link :to="{ name: 'Login' }" class="font-semibold text-indigo-600 hover:text-indigo-500">Log in</router-link>

            </p>
        </div>
    </Guest>
</template>