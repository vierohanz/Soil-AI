import os
import joblib
import numpy as np
import requests
import time
import pandas as pd

# model_path = os.path.join(os.getcwd(), 'app', 'AI', 'random_forest_model.joblib')
model_path = '/var/www/Soil-AI/app/AI/random_forest_model.joblib'

# Fungsi untuk memuat model
def load_model():
    try:
        print(f"Loading model from: {model_path}")
        model = joblib.load(model_path)
        return model
    except FileNotFoundError as e:
        print("Error: Model file not found. Please check the file path.")
        print(e)
        return None
    except Exception as e:
        print("An unexpected error occurred while loading the model.")
        print(e)
        return None

# Fungsi untuk mengambil data dari API dan melakukan prediksi
def get_and_predict(model):
    api_url = "https://soilapi.hcorp.my.id/api/get_today_collect_data"
    response = requests.get(api_url)

    if response.status_code == 200:
        data = response.json()['data']

        predictions = []
        all_binary_predictions = []
        all_probabilities = []

        for record in data:
            input_data = pd.DataFrame([[float(record['temperature']),
                                        float(record['air_humidity']),
                                        float(record['soil_humidity'])]],
                                       columns=["temperature", "air_humidity", "soil_humidity"])

            print(f"Input data for record {record['id']}: {input_data}")

            if input_data.shape[1] != model.n_features_in_:
                raise ValueError(f"Model expects {model.n_features_in_} features, but got {input_data.shape[1]} features.")


            binary_prediction = model.predict(input_data)[0]
            probabilities = model.predict_proba(input_data)[0]

            print(f"Prediction for record {record['id']}: {binary_prediction}")
            print(f"Probabilities for record {record['id']}: Tidak Siram: {probabilities[0]} | Siram: {probabilities[1]}")

            prediction_message = "siram" if binary_prediction == 1 else "tidak siram"
            predictions.append({
                "record_id": record['id'],
                "prediction": prediction_message,
                "probabilities": {
                    "tidak_siram": probabilities[0],
                    "siram": probabilities[1]
                }
            })

            all_binary_predictions.append(binary_prediction)
            all_probabilities.append({"tidak_siram": probabilities[0], "siram": probabilities[1]})

        print("Prediksi (Binary):", all_binary_predictions)
        print("Probabilitas:", all_probabilities)

        predictions.sort(key=lambda x: x['record_id'])
        if predictions:
            send_predictions(predictions)

    else:
        print(f"Failed to fetch data from API: {response.status_code}")


# Fungsi untuk mengirimkan seluruh hasil prediksi ke endpoint /send_message
def send_predictions(predictions):
    send_url = "https://soilapi.hcorp.my.id/api/send_message"
    headers = {'Content-Type': 'application/json'}

    for prediction in predictions:
        payload = {
            'message': prediction['prediction'],
            'prob_siram': prediction['probabilities']['tidak_siram'],
            'prob_tidak_siram': prediction['probabilities']['siram']
        }
        send_response = requests.post(send_url, json=payload, headers=headers)

        if send_response.status_code == 200:
            print(f"Prediction for record {prediction['record_id']} successfully sent!")
        else:
            print(f"Failed to send message for record {prediction['record_id']}: {send_response.status_code}")


# Main loop untuk terus memeriksa data
def run_continuously():
    model = load_model()
    if model is None:
        return

    while True:
        print("Checking for new data and predictions...")
        get_and_predict(model)
        time.sleep(60 * 10)

if __name__ == "__main__":
    run_continuously()
