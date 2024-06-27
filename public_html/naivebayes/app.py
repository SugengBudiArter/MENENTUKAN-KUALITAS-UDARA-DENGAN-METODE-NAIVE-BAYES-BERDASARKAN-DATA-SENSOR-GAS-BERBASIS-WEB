from flask import Flask, request, jsonify
import mysql.connector
from sklearn.naive_bayes import GaussianNB
import pandas as pd
from flask_cors import CORS

app = Flask(__name__)

CORS(app)

def train_model():
    # Connect to MySQL database
    try:
        conn = mysql.connector.connect(
            host='localhost',
            user='root', # ganti dengan user MySQL Anda
            password='', # ganti dengan password MySQL Anda
            database='udara' # ganti dengan nama database Anda
        )
        cursor = conn.cursor()
        cursor.execute("SELECT `COGT`, `NOxGT`, `NO2GT`, `C6H6GT`, `PT08S5O3`, `T`, `RH`, `label` FROM datalatih")
        rows = cursor.fetchall()
        cursor.close()
        conn.close()

        # Load data into a DataFrame
        df = pd.DataFrame(rows, columns=['COGT', 'NOxGT', 'NO2GT', 'C6H6GT', 'PT08S5O3', 'T', 'RH', 'label'])

        # Split features and labels
        X = df[['COGT', 'NOxGT', 'NO2GT', 'C6H6GT', 'PT08S5O3', 'T', 'RH']]
        Y = df['label']

        # Train model
        model = GaussianNB()
        model.fit(X, Y)
        return model
    except mysql.connector.Error as err:
        print("Error while connecting to MySQL:", err)
        return None

model = train_model()

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.json
        input_data = [[
            data['CO'], data['NOx'], data['NO2'], data['C6H6'], data['PT08S5'], data['T'], data['RH']
        ]]
        prediction = model.predict(input_data)[0]

        # Insert data into MySQL
        try:
            conn = mysql.connector.connect(
                host='localhost',
                user='root', # ganti dengan user MySQL Anda
                password='', # ganti dengan password MySQL Anda
                database='udara' # ganti dengan nama database Anda
            )
            cursor = conn.cursor()
            cursor.execute(
                "INSERT INTO sensordata (lokasi, waktu, `CO`, `NOx`, `NO2`, `C6H6`, `PT08S5`, T, RH, kualitasudara) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                (data['lokasi'], data['waktu'], data['CO'], data['NOx'], data['NO2'], data['C6H6'], data['PT08S5'], data['T'], data['RH'], prediction)
            )
            conn.commit()
            cursor.close()
            conn.close()
        except mysql.connector.Error as err:
            return jsonify({'status': 'error', 'message': str(err)}), 500

        return jsonify({'status': 'success', 'prediction': prediction}), 200
    except Exception as e:
        return jsonify({'status': 'error', 'message': str(e)}), 500
    
    
@app.route('/update', methods=['POST'])
def update():
    try:
        data = request.json
        input_data = [
            data['CO'], data['NOx'], data['NO2'], data['C6H6'], data['PT08S5'], data['T'], data['RH']
        ]
        prediction = model.predict([input_data])[0]

        # Update record in MySQL database
        conn = mysql.connector.connect(
            host='localhost',
            user='root',
            password='',
            database='udara'
        )
        cursor = conn.cursor()

        cursor.execute(
            "UPDATE sensordata SET lokasi=%s, waktu=%s, `CO`=%s, `NOx`=%s, `NO2`=%s, `C6H6`=%s, `PT08S5`=%s, T=%s, RH=%s, kualitasudara=%s WHERE id_sensorData=%s",
            (data['lokasi'], data['waktu'], data['CO'], data['NOx'], data['NO2'], data['C6H6'], data['PT08S5'], data['T'], data['RH'], prediction, data['id_sensorData'])
        )

        conn.commit()
        cursor.close()
        conn.close()

        return jsonify({'status': 'success', 'message': 'Data berhasil diperbarui.'}), 200
    except Exception as e:
        return jsonify({'status': 'error', 'message': str(e)}), 500

if __name__ == '__main__':
    app.run(port=5000)
