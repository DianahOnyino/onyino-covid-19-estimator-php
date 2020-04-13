import axios from "axios";
const FIELDS = {
  "ch-1": ["currentlyInfected", "infectionsByRequestedTime"],
  "ch-2": ["severeCasesByRequestedTime", "hospitalBedsByRequestedTime"],
  "ch-3": [
    "casesForICUByRequestedTime",
    "casesForVentilatorsByRequestedTime",
    "dollarsInFlight",
  ],
};
export const randomInt = (e, t = 1) =>
  Math.floor(Math.random() * (e - t + 1) + t);
export const logsSplitr = (e) => e.split(/\n+/);
export const countLogEntries = (e = "", t = logsSplitr) => t(e).length;
export const indexOfContentType = (e, t) =>
  e && e["content-type"] ? e["content-type"].indexOf(t) : -1;
export const getImpactDataStructure = (e) =>
  (e ? FIELDS[e] : Object.values(FIELDS).flat()).reduce(
    (e, t) => (
      (e.impact[t] = expect.any(Number)),
      (e.severeImpact[t] = expect.any(Number)),
      e
    ),
    { impact: {}, severeImpact: {} }
  );
export const valueOnFields = (e, t, s) =>
  (s ? FIELDS[s] : Object.values(FIELDS).flat()).reduce(
    (s, o) => (
      s.push([e.impact[o], t.impact[o]]),
      s.push([e.severeImpact[o], t.severeImpact[o]]),
      s
    ),
    []
  );
export const mockEstimationFor = async (e) =>
  axios.get(
    `https://us-central1-buildforsdg.cloudfunctions.net/api/gen/covid-19-scenario/${e.toLowerCase()}`
  );


// TESTS
import axios from "axios";
import propertiesReader from "properties-reader";
import {
  randomInt,
  logsSplitr,
  valueOnFields,
  countLogEntries,
  mockEstimationFor,
  indexOfContentType,
  getImpactDataStructure,
} from "../test-utils.js";
let api,
  timeout = 3e4;
const periodTypes = [["days"], ["weeks"], ["months"]],
  formats = [
    ["json", "application/json"],
    ["xml", "application/xml"],
  ],
  logPtrn = /^(GET|POST)\s+\/api\/v1\/on-covid-19(\/json|\/xml|\/logs)?\s+\d{3}\s+\d{2,}ms$/;
describe("on-covid-19 >> Challenge-5", () => {
  beforeAll(async () => {
    const t = propertiesReader("./app.properties");
    api = t.get("backend.rest");
  }),
    test("app.properties file has backend REST API URL", async () => {
      expect(api).toBeTruthy(),
        expect(api).not.toBe("https://jsonplaceholder.typicode.com/todos");
    }),
    test.each(periodTypes)(
      "REST API estimates correctly, in %s",
      async (t) => {
        const e = await mockEstimationFor(t),
          { data: a, estimate: o } = e.data,
          s = await axios.post(api, a),
          { data: i, impact: p, severeImpact: r } = s.data;
        expect(i).toBeTruthy(), expect(p).toBeTruthy(), expect(r).toBeTruthy();
        const n = { data: i, impact: p, severeImpact: r };
        expect(n).toMatchObject(getImpactDataStructure()),
          valueOnFields(n, o).forEach(([t, e]) => {
            expect(t).toBe(e);
          });
      },
      timeout
    ),
    test.each(formats)(
      "API handles request for %s format",
      async (t, e) => {
        const a = await mockEstimationFor("weeks"),
          { data: o } = a.data,
          { headers: s } = await axios.post(`${api}/${t}`, o),
          i = indexOfContentType(s, e);
        expect(i).toBeGreaterThanOrEqual(0);
      },
      timeout
    ),
    test(
      "API provides plain text logs at /logs",
      async () => {
        const { data: t } = await axios.get(`${api}/logs`, {
            responseType: "text",
          }),
          e = countLogEntries(t),
          a = await mockEstimationFor("weeks"),
          { data: o } = a.data;
        await axios.post(`${api}`, o),
          await axios.post(`${api}/json`, o),
          await axios.post(`${api}/xml`, o);
        const { headers: s, data: i } = await axios.get(`${api}/logs`, {
            responseType: "text",
          }),
          p = indexOfContentType(s, "text/plain");
        expect(p).toBeGreaterThanOrEqual(0);
        const r = countLogEntries(i);
        expect(r).toBeGreaterThanOrEqual(e + 3);
        const n = logsSplitr(i),
          c = n[randomInt(n.length - 1, 0)];
        expect(logPtrn.test(c)).toBeTruthy();
      },
      timeout
    );
});
