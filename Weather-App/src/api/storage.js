import AsyncStorage from '@react-native-async-storage/async-storage';

const FAVORITE_LOCATIONS_KEY = 'favoriteLocations';

export const saveFavoriteLocations = async (locations) => {
  try {
    const jsonValue = JSON.stringify(locations);
    await AsyncStorage.setItem(FAVORITE_LOCATIONS_KEY, jsonValue);
  } catch (e) {
    console.error('Error saving favorite locations to storage:', e);
  }
};

export const getFavoriteLocations = async () => {
  try {
    const jsonValue = await AsyncStorage.getItem(FAVORITE_LOCATIONS_KEY);
    return jsonValue != null ? JSON.parse(jsonValue) : [];
  } catch (e) {
    console.error('Error reading favorite locations from storage:', e);
    return [];
  }
};
