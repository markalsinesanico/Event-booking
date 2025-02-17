import { useProfileStore } from '@/store/profileStore';
import axios from 'axios';

export const useProfile = () => {
  const profileStore = useProfileStore();

  const fetchUserProfile = async () => {
    try {
      const token = localStorage.getItem('token');
      const response = await axios.get('/api/user/profile', {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });
      if (response.data.status === 'success') {
        profileStore.updateUserData(response.data.user);
        if (response.data.user.profile_image) {
          profileStore.updateProfileImage(response.data.user.profile_image);
        }
      }
    } catch (error) {
      console.error('Error fetching profile:', error);
    }
  };

  return {
    fetchUserProfile
  };
}; 