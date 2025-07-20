import React from 'react';
import { View, Text, ScrollView, TouchableOpacity, StyleSheet, Image } from 'react-native';
import Icon from 'react-native-vector-icons/Ionicons';

const CurrentWeatherScreen = ({ currentWeatherData, currentLocationName, onSearchSubmit, isCurrentLocationFavorite, onToggleFavorite, onRefresh }) => {

  const getWeatherCondition = (data) => {
    return data?.weather?.[0]?.description || 'Unknown';
  };

  const getWeatherIconUrl = (iconCode) => {
    return iconCode ? `https://openweathermap.org/img/wn/${iconCode}@2x.png` : null;
  };

  return (
    <View style={currentWeatherStyles.fullScreenContainer}>
      <ScrollView contentContainerStyle={currentWeatherStyles.scrollViewContent} style={currentWeatherStyles.scrollView}>
        <View style={currentWeatherStyles.locationContainer}>
          <Icon name="location-outline" size={20} color="#A0A0A0" />
          <Text style={currentWeatherStyles.locationText}>{currentLocationName || 'Loading...'}</Text>
          <TouchableOpacity onPress={onToggleFavorite} style={currentWeatherStyles.favoriteButton}>
            <Icon
              name={isCurrentLocationFavorite ? 'heart' : 'heart-outline'}
              size={24}
              color={isCurrentLocationFavorite ? '#FF5C5C' : '#A0A0A0'}
            />
          </TouchableOpacity>
          {onRefresh && (
            <TouchableOpacity onPress={onRefresh} style={currentWeatherStyles.refreshButton}>
              <Icon name="refresh-outline" size={24} color="#A0A0A0" />
            </TouchableOpacity>
          )}
        </View>

        {currentWeatherData ? (
          <>
            <View style={currentWeatherStyles.mainWeatherContainer}>
              {currentWeatherData.weather?.[0]?.icon ? (
                <Image
                  style={currentWeatherStyles.weatherIcon}
                  source={{ uri: getWeatherIconUrl(currentWeatherData.weather[0].icon) }}
                />
              ) : (
                <View style={currentWeatherStyles.emptyMainIconPlaceholder} />
              )}
              <Text style={currentWeatherStyles.temperatureText}>{Math.round(currentWeatherData.temp)}°</Text>
            </View>
            <Text style={currentWeatherStyles.weatherDescription}>
              {getWeatherCondition(currentWeatherData)}
            </Text>

            <View style={currentWeatherStyles.detailsContainer}>
              <View style={currentWeatherStyles.detailItem}>
                <Text style={currentWeatherStyles.detailLabel}>Humidity</Text>
                <Text style={currentWeatherStyles.detailValue}>{currentWeatherData.humidity}%</Text>
              </View>
              <View style={currentWeatherStyles.detailItem}>
                <Text style={currentWeatherStyles.detailLabel}>Pressure</Text>
                <Text style={currentWeatherStyles.detailValue}>{currentWeatherData.pressure} hPa</Text>
              </View>
              <View style={currentWeatherStyles.detailItem}>
                <Text style={currentWeatherStyles.detailLabel}>Wind Speed</Text>
                <Text style={currentWeatherStyles.detailValue}>{currentWeatherData.wind_speed} m/s</Text>
              </View>
            </View>
          </>
        ) : (
          <View style={currentWeatherStyles.noDataContainer}>
            <Text style={currentWeatherStyles.noDataText}>Weather data not available.</Text>
            <Text style={currentWeatherStyles.noDataSubText}>Use the search bar or add a favorite location.</Text>
          </View>
        )}
      </ScrollView>
    </View>
  );
};

const currentWeatherStyles = StyleSheet.create({
  fullScreenContainer: {
    flex: 1,
    backgroundColor: '#213366',
  },
  scrollView: {
    flex: 1,
  },
  scrollViewContent: {
    flexGrow: 1,
    justifyContent: 'space-between',
    paddingBottom: 20,
  },
  locationContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 20,
    paddingHorizontal: 20,
  },
  locationText: {
    color: '#FFFFFF',
    fontSize: 16,
    marginLeft: 5,
    flex: 1,
  },
  favoriteButton: {
    marginLeft: 10,
    padding: 5,
  },
  refreshButton: {
    marginLeft: 10,
    padding: 5,
  },
  mainWeatherContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginVertical: 30,
  },
  weatherIcon: {
    width: 80,
    height: 80,
  },
  emptyMainIconPlaceholder: {
    width: 80,
    height: 80,
  },
  temperatureText: {
    color: '#FFFFFF',
    fontSize: 90,
    fontWeight: 'bold',
    marginLeft: 15,
  },
  weatherDescription: {
    color: '#FFFFFF',
    fontSize: 20,
    textAlign: 'center',
    marginBottom: 20,
    fontWeight: 'bold',
    textTransform: 'capitalize',
  },
  detailsContainer: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    marginHorizontal: 20,
    marginBottom: 40,
  },
  detailItem: {
    alignItems: 'center',
  },
  detailLabel: {
    color: '#A0A0A0',
    fontSize: 14,
    marginBottom: 5,
  },
  detailValue: {
    color: '#FFFFFF',
    fontSize: 18,
    fontWeight: 'bold',
  },
  noDataContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 50,
    paddingHorizontal: 30,
  },
  noDataText: {
    color: '#A0A0A0',
    fontSize: 20,
    fontWeight: 'bold',
    marginTop: 10,
    textAlign: 'center',
  },
  noDataSubText: {
    color: '#808080',
    fontSize: 14,
    textAlign: 'center',
    marginTop: 5,
  },
});

export default CurrentWeatherScreen;
