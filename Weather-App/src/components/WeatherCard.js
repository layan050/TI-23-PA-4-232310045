import React from 'react';
import { View, Text, StyleSheet, Image } from 'react-native';

const WeatherCard = ({ day, date, humidity, tempHigh, tempLow, condition, iconCode, hideDayAndDate, isFavoriteCard }) => {

  const getWeatherIconUrl = (code) => {
    return code ? `https://openweathermap.org/img/wn/${code}@2x.png` : null;
  };

  return (
    <View style={[weatherCardStyles.card, isFavoriteCard && weatherCardStyles.favoriteCardOverride]}>
      {!hideDayAndDate && (
        <>
          <Text style={weatherCardStyles.dayText}>{day}</Text>
          <Text style={weatherCardStyles.dateText}>{date}</Text>
        </>
      )}
      <View style={weatherCardStyles.detailsRow}>
        {iconCode ? (
          <Image
            style={weatherCardStyles.weatherIcon}
            source={{ uri: getWeatherIconUrl(iconCode) }}
          />
        ) : (
          <View style={weatherCardStyles.emptyIconPlaceholder} />
        )}
        <Text style={weatherCardStyles.humidityText}>Humidity</Text>
        <Text style={weatherCardStyles.humidityValue}>{humidity}%</Text>
        <View style={weatherCardStyles.tempContainer}>
          <Text style={weatherCardStyles.tempHigh}>{tempHigh}°</Text>
          <Text style={weatherCardStyles.tempLow}>/{tempLow}°</Text>
        </View>
      </View>
    </View>
  );
};

const weatherCardStyles = StyleSheet.create({
  card: {
    backgroundColor: 'transparent',
    borderRadius: 10,
    padding: 15,
    paddingTop: 0,
  },
  favoriteCardOverride: {
    marginHorizontal: 0,
    marginBottom: 0,
  },
  dayText: {
    color: '#FFFFFF',
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 5,
  },
  dateText: {
    color: '#A0A0A0',
    fontSize: 14,
    marginBottom: 10,
  },
  detailsRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  weatherIcon: {
    width: 40,
    height: 30,
    marginRight: 10,
  },
  emptyIconPlaceholder: {
    width: 40,
    height: 30,
    marginRight: 10,
  },
  humidityText: {
    color: '#A0A0A0',
    fontSize: 14,
    marginRight: 5,
  },
  humidityValue: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: 'bold',
  },
  tempContainer: {
    flexDirection: 'row',
    alignItems: 'baseline',
  },
  tempHigh: {
    color: '#FFFFFF',
    fontSize: 24,
    fontWeight: 'bold',
  },
  tempLow: {
    color: '#A0A0A0',
    fontSize: 18,
  },
});

export default WeatherCard;
