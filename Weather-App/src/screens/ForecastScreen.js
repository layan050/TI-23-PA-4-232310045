import React from 'react';
import { View, Text, ScrollView, StyleSheet } from 'react-native';
import WeatherCard from '../components/WeatherCard';
import moment from 'moment';

const ForecastScreen = ({ dailyForecastData, currentLocationName }) => {

  const getDayName = (timestamp) => {
    return moment.unix(timestamp).format('dddd');
  };

  const getFormattedDate = (timestamp) => {
    return moment.unix(timestamp).format('MMM D');
  };

  const forecastToShow = Array.isArray(dailyForecastData) ? dailyForecastData.slice(1, 8) : [];

  return (
    <View style={forecastScreenStyles.fullScreenContainer}>
      <ScrollView contentContainerStyle={forecastScreenStyles.scrollViewContent} style={forecastScreenStyles.scrollView}>
        <Text style={forecastScreenStyles.forecastHeader}>7-Day Forecast for {currentLocationName || 'Loading...'}</Text>

        {forecastToShow.length > 0 ? (
          forecastToShow.map((dayData, indeks) => (
            <WeatherCard
              key={indeks}
              day={getDayName(dayData.dt)}
              date={getFormattedDate(dayData.dt)}
              humidity={dayData.humidity}
              tempHigh={Math.round(dayData.temp.max)}
              tempLow={Math.round(dayData.temp.min)}
              condition={dayData.weather[0].description}
              iconCode={dayData.weather[0].icon}
            />
          ))
        ) : (
          <View style={forecastScreenStyles.noDataContainer}>
            <Text style={forecastScreenStyles.noDataText}>Forecast data not available.</Text>
          </View>
        )}

        <View style={forecastScreenStyles.bottomSection} />
      </ScrollView>
    </View>
  );
};

const forecastScreenStyles = StyleSheet.create({
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
  },
  forecastHeader: {
    color: '#FFFFFF',
    fontSize: 18,
    fontWeight: 'bold',
    marginHorizontal: 20,
    marginBottom: 15,
    marginTop: 10,
  },
  noDataContainer: {
    alignItems: 'center',
    paddingVertical: 50,
  },
  noDataText: {
    color: '#A0A0A0',
    fontSize: 16,
    textAlign: 'center',
  },
  bottomSection: {
    paddingBottom: 20,
  },
});

export default ForecastScreen;
