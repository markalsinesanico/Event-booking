import { ref, reactive } from 'vue';

const state = reactive({
  profileImage: '/img/defaultProfile.png',
  userData: {
    firstname: '',
    lastname: '',
    email: '',
    phone: '',
    profile_image: null
  }
});

export const useProfileStore = () => {
  const updateProfileImage = (imagePath) => {
    if (!imagePath) {
      state.profileImage = '/img/defaultProfile.png';
      return;
    }

    if (imagePath.startsWith('http')) {
      state.profileImage = imagePath;
    } else if (imagePath.startsWith('/storage')) {
      state.profileImage = imagePath;
    } else {
      state.profileImage = `/storage/profile_images/${imagePath}`;
    }
    
    state.userData.profile_image = imagePath;
  };

  const updateUserData = (userData) => {
    state.userData = { ...state.userData, ...userData };
  };

  return {
    state,
    updateProfileImage,
    updateUserData
  };
}; 