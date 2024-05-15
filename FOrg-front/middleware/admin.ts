export default defineNuxtRouteMiddleware(async (to, from) =>{
    const user = useSanctumUser();

    if(!user.value.roles.find(value => value.name == 'admin')){
        return navigateTo('/');
    };
})